<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service;

use Digip\AppDeployBundle\Interfaces\CiProcessorInterface;
use Digip\AppDeployBundle\Service\CiAppTypeSettingsService;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsCiAppTypeSettings;
use Digip\DeployBundle\Entity\Application;
use Digip\DeployBundle\Entity\AppEnvironment;
use Digip\DeployBundle\Entity\Server;

class JenkinsProcessor implements CiProcessorInterface
{
    /**
     * @var Jenkins
     */
    private $jenkins;
    /**
     * @var CiAppTypeSettingsService
     */
    private $ciAppTypeSettingsService;

    /**
     * JenkinsProcessor constructor.
     *
     * @param CiAppTypeSettingsService $ciAppTypeSettingsService
     * @param Jenkins                  $jenkins
     */
    public function __construct(CiAppTypeSettingsService $ciAppTypeSettingsService, Jenkins $jenkins)
    {
        $this->jenkins = $jenkins;
        $this->ciAppTypeSettingsService = $ciAppTypeSettingsService;
    }

    /**
     * @param Server[] $servers
     *
     * @return array
     */
    private function getServersInfo($servers)
    {
        $serverLine = '';
        $workerIp = '';
        /** @var Server $server */
        foreach ($servers as $server) {
            $serverLine .= $server->getIp().' ';
            if ($server->isTaskServer()) {
                $workerIp = $server->getIp();
            }
        }

        return ['serverLine' => $serverLine, 'workerIp' => $workerIp];
    }

    private function addRoboTokens($type, $environments, $servers, $tokens)
    {
        $roboScript = $this->buildRoboScript($type, $environments, $servers);
        $roboTokens = ['__ROBO_SCRIPT__' => $roboScript];

        return array_merge($tokens, $roboTokens);
    }

    /**
     * @param $type
     * @param AppEnvironment[] $environments
     * @param Server[]         $servers
     *
     * @return string
     */
    private function buildRoboScript($type, $environments, $servers)
    {
        $serverInfo = $this->getServersInfo($servers);
        $serverLine = $serverInfo['serverLine'];
        $workerIp = $serverInfo['workerIp'];

        if ($type === 'deploy') {
            $appInfo = $this->getAppInfo($environments[0]);
            $appName = $appInfo['appName'];
            $siteName = $appInfo['siteName'];
            $user = $appInfo['user'];

            $roboscript = <<<ROBOSCRIPT
#!/bin/sh
composer install --optimize-autoloader --apcu-autoloader
PRIVATE_KEY="\$JENKINS_HOME/.ssh/id_rsa"
vendor/bin/robo digipolis:deploy-drupal8 $serverLine $user \${PRIVATE_KEY} --app=$appName --worker=$workerIp --profile='digipolis' --site-name='$siteName' --config-import
code=$?
exit \$code
ROBOSCRIPT;
        }

        if ($type === 'sync') {
            foreach ($servers as $server) {
                if ($server->getEnvironment() === $environments[0]->getNameCanonical()) {
                    $hostFrom = $server->getIp();
                } elseif ($server->getEnvironment() === $environments[1]->getNameCanonical()) {
                    $hostTo = $server->getIp();
                }
            }

            $appInfoFrom = $this->getAppInfo($environments[0]);
            $appNameFrom = $appInfoFrom['appName'];
            $userFrom = $appInfoFrom['user'];

            $appInfoTo = $this->getAppInfo($environments[1]);
            $appNameTo = $appInfoTo['appName'];
            $userTo = $appInfoTo['user'];

            $roboscript = <<<ROBOSCRIPT
#!/bin/sh
composer install
PRIVATE_KEY="\$JENKINS_HOME/.ssh/id_rsa"
vendor/bin/robo digipolis:sync-drupal8 $userFrom $hostFrom \${PRIVATE_KEY} $userTo $hostTo \${PRIVATE_KEY} $appNameFrom $appNameTo
ROBOSCRIPT;
        }

        if ($type === 'validate') {
            $roboscript = <<<ROBOSCRIPT
#!/bin/sh
composer install
vendor/bin/robo digipolis:validate-drupal8
ROBOSCRIPT;
        }

        return $roboscript;
    }

    private function getAppInfo(AppEnvironment $environment)
    {
        $appName = substr($environment->getApplication()->getNameCanonical(), 0, 14);
        $siteName = $appName;
        if (null === $environment->getApplication()->getParent()) {
            $appName = 'default';
        }
        $user = $environment->getServerSettings()->getUser();

        return ['appName' => $appName, 'siteName' => $siteName, 'user' => $user];
    }

    /** Creates jenkins jobs by copying templates and replacing config variables
     * @param AppEnvironment $appEnvironment
     * @param array|Server[] $servers
     * @param bool           $override
     *
     * @return bool true if created false if job already present or no servers given
     */
    public function createDeployJob(AppEnvironment $appEnvironment, array $servers, $override = false)
    {
        if (!count($servers)) {
            return false;
        }

        /** @var JenkinsCiAppTypeSettings $ciAppTypeSettings */
        $ciAppTypeSettings = $this->ciAppTypeSettingsService->getSettingsForApp($appEnvironment->getApplication());
        $src = $ciAppTypeSettings->getDeployJobTemplate();

        $dest = $appEnvironment->getApplication()->getNameCanonical().'_deploy_'.$appEnvironment->getNameCanonical();

        $e = $appEnvironment->getNameCanonical();
        $phing = array();
        $names = array();
        $remoteWorker = null;
        foreach ($servers as $k => $s) {
            $name = $e.$k;
            $names[] = $name;
            $phing = array_merge($phing, array(
                "remote.env.$name.host=".$s->getIp(),
                "remote.env.$name.user=".$appEnvironment->getServerSettings()->getUser(),
                "remote.env.$name.privkey=".'~/.ssh/id_rsa',
                "remote.env.$name.uri=".'http://'.$appEnvironment->getPreferredDomain(),
            ));
            if ($s->isTaskServer()) {
                $remoteWorker = $name;
            }
        }

        if (null !== $appEnvironment->getApplication()->getParent()) {
            $application_name = substr($appEnvironment->getApplication()->getNameCanonical(), 0, 14);
            $phing = array_merge($phing, [
                'remote.releases.dir='."~/apps/$application_name/releases",
                'remote.current.dir='."~/apps/$application_name/current",
                'remote.backups.dir='."~/apps/$application_name/backups",
                'remote.files.dir='."~/apps/$application_name/files",
                'remote.config.dir='."~/apps/$application_name/config",
            ]);
        }

        $phing = array_merge($phing, array(
            'remote.env.list='.implode(',', $names),
            'remote.env.worker='.$remoteWorker,
        ));
        $phing = implode("\n", $phing);

        $tokens = array(
            '__GIT_REPO__' => $appEnvironment->getApplication()->getGitRepoFull(),
            '__GIT_REPO_REF__' => $appEnvironment->getGitRef(),
            '__PHING_TARGETS__' => 'deploy',
            '__PHING_PROPERTIES__' => $phing,
        );

        $tokens = $this->addRoboTokens('deploy', [$appEnvironment], $servers, $tokens);

        $this->jenkins->copyJenkinsJob($src, $dest, $tokens, $appEnvironment->isDevPermissions(), $override);

        foreach ($ciAppTypeSettings->getDeployJobGroovyScripts() as $deployJobGroovyScript) {
            $this->jenkins->runGroovyScript($deployJobGroovyScript->getContent(), [], $appEnvironment->getApplication()->getNameCanonical().'_deploy_'.$appEnvironment->getNameCanonical());
        }

        return true;
    }

    /**
     * Creates jenkins jobs by copying templates and replacing config variables.
     *
     * @param AppEnvironment $appEnvironment
     * @param array|Server[] $servers
     * @param bool           $override
     *
     * @return bool true if created false if job already present or no servers given
     */
    public function createRevertJob(AppEnvironment $appEnvironment, array $servers, $override = false)
    {
        if (!count($servers)) {
            return false;
        }

        /** @var JenkinsCiAppTypeSettings $ciAppTypeSettings */
        $ciAppTypeSettings = $this->ciAppTypeSettingsService->getSettingsForApp($appEnvironment->getApplication());
        $src = $ciAppTypeSettings->getRevertJobTemplate();

        $dest = $appEnvironment->getApplication()->getNameCanonical().'_revert_'.$appEnvironment->getNameCanonical();

        $e = $appEnvironment->getNameCanonical();
        $phing = array();
        $names = array();
        $remoteWorker = null;
        foreach ($servers as $k => $s) {
            $name = $e.$k;
            $names[] = $name;
            $phing = array_merge($phing, array(
                "remote.env.$name.host=".$s->getIp(),
                "remote.env.$name.user=".$appEnvironment->getServerSettings()->getUser(),
                "remote.env.$name.privkey=".'~/.ssh/id_rsa',
                "remote.env.$name.uri=".'http://'.$appEnvironment->getPreferredDomain(),
            ));
            if ($s->isTaskServer()) {
                $remoteWorker = $name;
            }
        }

        if (null !== $appEnvironment->getApplication()->getParent()) {
            $application_name = substr($appEnvironment->getApplication()->getNameCanonical(), 0, 14);
            $phing = array_merge($phing, array(
                'remote.releases.dir='."~/apps/$application_name/releases",
                'remote.current.dir='."~/apps/$application_name/current",
                'remote.backups.dir='."~/apps/$application_name/backups",
                'remote.files.dir='."~/apps/$application_name/files",
                'remote.config.dir='."~/apps/$application_name/config",
            ));
        }

        $phing = array_merge($phing, array(
            'remote.env.list='.implode(',', $names),
            'remote.env.worker='.$remoteWorker,
        ));
        $phing = implode("\n", $phing);

        $tokens = array(
            '__GIT_REPO__' => $appEnvironment->getApplication()->getGitRepoFull(),
            '__GIT_REPO_REF__' => $appEnvironment->getGitRef(),
            '__PHING_TARGETS__' => 'revert',
            '__PHING_PROPERTIES__' => $phing,
        );

        $this->jenkins->copyJenkinsJob($src, $dest, $tokens, $appEnvironment->isDevPermissions(), $override);

        foreach ($ciAppTypeSettings->getRevertJobGroovyScripts() as $jobGroovyScript) {
            $this->jenkins->runGroovyScript($jobGroovyScript->getContent(), [], $appEnvironment->getApplication()->getNameCanonical().'_revert_'.$appEnvironment->getNameCanonical());
        }

        return true;
    }

    /**
     * @param AppEnvironment $appEnvironmentFrom
     * @param AppEnvironment $appEnvironmentTo
     * @param array|Server[] $servers
     * @param bool           $override
     *
     * @return bool
     */
    public function createSyncJob(AppEnvironment $appEnvironmentFrom, AppEnvironment $appEnvironmentTo, array $servers, $override = false)
    {
        /** @var JenkinsCiAppTypeSettings $ciAppTypeSettings */
        $ciAppTypeSettings = $this->ciAppTypeSettingsService->getSettingsForApp($appEnvironmentTo->getApplication());
        $src = $ciAppTypeSettings->getSyncJobTemplate();

        $dest = $appEnvironmentFrom->getApplication()->getNameCanonical().'_sync_'.$appEnvironmentFrom->getNameCanonical().'_to_'.$appEnvironmentTo->getNameCanonical();

        $hostFrom = null;
        $hostTo = null;
        foreach ($servers as $server) {
            if ($server->getEnvironment() === $appEnvironmentFrom->getNameCanonical()) {
                $hostFrom = $server->getIp();
            } elseif ($server->getEnvironment() === $appEnvironmentTo->getNameCanonical()) {
                $hostTo = $server->getIp();
            }
        }

        if (null === $hostFrom || null === $hostTo) {
            return false;
        }

        $eFrom = $appEnvironmentFrom->getNameCanonical();
        $eTo = $appEnvironmentTo->getNameCanonical();
        $phing = array(
            "remote.env.source=$eFrom",
            "remote.env.list=$eTo",
            "remote.env.$eFrom.host=".$hostFrom,
            "remote.env.$eFrom.user=".$appEnvironmentFrom->getServerSettings()->getUser(),
            "remote.env.$eFrom.privkey=".'~/.ssh/id_rsa',
            "remote.env.$eFrom.uri=".'http://'.$appEnvironmentFrom->getPreferredDomain(),
            "remote.env.$eTo.host=".$hostTo,
            "remote.env.$eTo.user=".$appEnvironmentTo->getServerSettings()->getUser(),
            "remote.env.$eTo.privkey=".'~/.ssh/id_rsa',
            "remote.env.$eTo.uri=".'http://'.$appEnvironmentTo->getPreferredDomain(),
            'pull.type=db,files',
        );

        // This part presumes that if the from has a parent, the target has too.
        if (null !== $appEnvironmentFrom->getApplication()->getParent()) {
            $application_name = substr($appEnvironmentFrom->getApplication()->getNameCanonical(), 0, 14);
            $phing = array_merge($phing, array(
                'remote.releases.dir='."~/apps/$application_name/releases",
                'remote.current.dir='."~/apps/$application_name/current",
                'remote.backups.dir='."~/apps/$application_name/backups",
                'remote.files.dir='."~/apps/$application_name/files",
                'remote.config.dir='."~/apps/$application_name/config",
            ));
        }

        $phing = implode("\n", $phing);

        $tokens = array(
            '__GIT_REPO__' => $appEnvironmentFrom->getApplication()->getGitRepoFull(),
            '__GIT_REPO_REF__' => $appEnvironmentFrom->getApplication()->getProdAppEnvironment()->getGitRef(),
            '__PHING_TARGETS__' => 'pull',
            '__PHING_PROPERTIES__' => $phing,
        );

        $tokens = $this->addRoboTokens('sync', [$appEnvironmentFrom, $appEnvironmentTo], $servers, $tokens);

        $this->jenkins->copyJenkinsJob($src, $dest, $tokens, true, $override);
        foreach ($ciAppTypeSettings->getSyncJobGroovyScripts() as $jobGroovyScript) {
            $this->jenkins->runGroovyScript($jobGroovyScript->getContent(), [], $appEnvironmentFrom->getApplication()->getNameCanonical().'_sync_'.$appEnvironmentFrom->getNameCanonical().'_to_'.$appEnvironmentTo->getNameCanonical());
        }

        return true;
    }

    /**
     * @param AppEnvironment $appEnvironment
     * @param array|Server[] $servers
     * @param bool           $override
     *
     * @return bool
     */
    public function createDumpJob(AppEnvironment $appEnvironment, array $servers, $override = false)
    {
        /** @var JenkinsCiAppTypeSettings $ciAppTypeSettings */
        $ciAppTypeSettings = $this->ciAppTypeSettingsService->getSettingsForApp($appEnvironment->getApplication());
        $src = $ciAppTypeSettings->getDumpJobTemplate();

        $dest = $appEnvironment->getApplication()->getNameCanonical().'_dump_'.$appEnvironment->getNameCanonical();

        $e = $appEnvironment->getNameCanonical();
        $phing = array();
        foreach ($servers as $k => $s) {
            if (!$s->isTaskServer()) {
                continue;
            }
            $name = $e.$k;
            $phing = array_merge($phing, array(
                "remote.env.source=$name",
                "remote.env.$name.host=".$s->getIp(),
                "remote.env.$name.user=".$appEnvironment->getServerSettings()->getUser(),
                "remote.env.$name.privkey=".'~/.ssh/id_rsa',
                "remote.env.$name.uri=".'http://'.$appEnvironment->getPreferredDomain(),
                'pull.type=db',
            ));
        }

        if (null !== $appEnvironment->getApplication()->getParent()) {
            $application_name = substr($appEnvironment->getApplication()->getNameCanonical(), 0, 14);
            $phing = array_merge($phing, array(
                'remote.releases.dir='."~/apps/$application_name/releases",
                'remote.current.dir='."~/apps/$application_name/current",
                'remote.backups.dir='."~/apps/$application_name/backups",
                'remote.files.dir='."~/apps/$application_name/files",
                'remote.config.dir='."~/apps/$application_name/config",
            ));
        }

        $phing = implode("\n", $phing);

        $tokens = array(
            '__GIT_REPO__' => $appEnvironment->getApplication()->getGitRepoFull(),
            '__GIT_REPO_REF__' => $appEnvironment->getApplication()->getProdAppEnvironment()->getGitRef(),
            '__PHING_TARGETS__' => 'pull',
            '__PHING_PROPERTIES__' => $phing,
        );

        $this->jenkins->copyJenkinsJob($src, $dest, $tokens, true, $override);
        foreach ($ciAppTypeSettings->getDumpJobGroovyScripts() as $jobGroovyScript) {
            $this->jenkins->runGroovyScript($jobGroovyScript->getContent(), [], $appEnvironment->getApplication()->getNameCanonical().'_dump_'.$appEnvironment->getNameCanonical());
        }

        return true;
    }

    /**
     * @param Application $app
     * @param bool        $override
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function createValidateJob(Application $app, $override = false)
    {
        /** @var JenkinsCiAppTypeSettings $ciAppTypeSettings */
        $ciAppTypeSettings = $this->ciAppTypeSettingsService->getSettingsForApp($app);
        $src = $ciAppTypeSettings->getValidateJobTemplate();

        $dest = $app->getNameCanonical().'_validate';

        $tokens = array(
            '__GIT_REPO__' => $app->getGitRepoFull(),
            '__GIT_REPO_REF__' => $app->getProdAppEnvironment()->getGitRef(),
            '__PHING_TARGETS__' => 'verify',
            '__PHING_PROPERTIES__' => '',
            '__TRIGGER_TIME_MINUTE__' => mt_rand(0, 59),
            '__TRIGGER_TIME_HOUR__' => '*/21',
        );

        $tokens = $this->addRoboTokens('validate', [], [], $tokens);

        $this->jenkins->copyJenkinsJob($src, $dest, $tokens, true, $override);
        foreach ($ciAppTypeSettings->getValidateJobGroovyScripts() as $jobGroovyScript) {
            $this->jenkins->runGroovyScript($jobGroovyScript->getContent(), [], $app->getNameCanonical().'_validate');
        }

        return true;
    }
}
