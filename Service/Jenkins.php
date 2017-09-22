<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service;

use DigipolisGent\Domainator9k\CoreBundle\Service\CiSettingsService;
use DigipolisGent\Domainator9k\CoreBundle\Service\CiTypeBuilder;
use DigipolisGent\Domainator9k\CoreBundle\Task\Factory as TaskFactory;

class Jenkins
{
    /**
     * @var array
     */
    protected $jenkinsJobs;

    /**
     * @var string
     */
    protected $jenkinsDir;
    /**
     * @var CiSettingsService
     */
    private $settingsService;
    /**
     * @var CiTypeBuilder
     */
    private $ciTypeBuilder;

    /**
     * @param CiTypeBuilder     $ciTypeBuilder
     * @param CiSettingsService $settingsService
     * @param string|null       $kernelDir
     *
     * @internal param Settings $settings
     */
    public function __construct(CiTypeBuilder $ciTypeBuilder, CiSettingsService $settingsService, $kernelDir = null)
    {
        if (!$kernelDir) {
            $this->jenkinsDir = realpath($kernelDir.'../bin');
        } else {
            $this->jenkinsDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/bin';
        }
        $this->settingsService = $settingsService;
        $this->ciTypeBuilder = $ciTypeBuilder;
    }

    /**
     * @param string $jobName
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function doesJenkinsJobExist($jobName)
    {
        if (!$this->jenkinsJobs) {
            // fetch jenkins jobs
            $listJobsTask = TaskFactory::create('DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Console\Jenkins', array(
                'settings' => $this->settingsService->getSettings($this->ciTypeBuilder->getType('jenkins')),
                'directory' => $this->jenkinsDir,
                'command' => 'list-jobs',
            ));
            $result = $listJobsTask->execute();

            if (!$result->isSuccess()) {
                throw new \RuntimeException('Failed to fetch jenkins jobs list');
            }

            $this->jenkinsJobs = explode("\n", trim($result->getData()));
        }

        return in_array($jobName, $this->jenkinsJobs, true);
    }

    /**
     * @param string $jobName
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function getJenkinsJobXml($jobName)
    {
        // fetch jenkins jobs xml
        $listJobsTask = TaskFactory::create('DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Console\Jenkins', array(
            'settings' => $this->settingsService->getSettings($this->ciTypeBuilder->getType('jenkins')),
            'directory' => $this->jenkinsDir,
            'command' => "get-job $jobName",
        ));
        $result = $listJobsTask->execute();

        if (!$result->isSuccess()) {
            throw new \RuntimeException('Failed to fetch jenkins job xml');
        }

        return $result->getData();
    }

    /**
     * @param string $jobName
     * @param string $xml
     *
     * @return bool
     */
    public function createJenkinsJobFromXml($jobName, $xml)
    {
        return $this->createOrUpdateJob($xml, $jobName, false);
    }

    protected function createOrUpdateJob($xml, $job, $override)
    {
        $exists = $this->doesJenkinsJobExist($job);

        if ($override || !$exists) {
            $command = ($exists ? 'update-job' : 'create-job').' '.$job;
            $task = TaskFactory::create('DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Console\Jenkins', array(
                'settings' => $this->settingsService->getSettings($this->ciTypeBuilder->getType('jenkins')),
                'directory' => $this->jenkinsDir,
                'command' => $command,
                'pipe_in' => escapeshellarg($xml),
            ));

            $result = $task->execute();

            if (!$result->isSuccess()) {
                throw new \RuntimeException('Failed to create jenkins job');
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $src
     * @param string $dest
     * @param array  $tokens
     * @param bool   $setDevPermissions
     * @param bool   $override
     *
     * @return bool
     */
    public function copyJenkinsJob($src, $dest, array $tokens = array(), $setDevPermissions = false, $override = false)
    {
        // job exists, we're done for now
        if (!$override && $this->doesJenkinsJobExist($dest)) {
            return false;
        }

        $jobXML = $this->getJenkinsJobXml($src);

        $jobXML = str_replace(
            array_keys($tokens),
            array_values($tokens),
            $jobXML
        );

        $created = $this->createOrUpdateJob($jobXML, $dest, $override);

        if ($created) {
            // Add the job to the "By project" view
            $this->runGroovy(
                __DIR__.'/../Resources/jenkins-groovy/project-view-add.groovy', array(
                '__JOB_NAME__' => $dest,
            ));

            if ($setDevPermissions) {
                // set job permissions
                $this->runGroovy(
                    __DIR__.'/../Resources/jenkins-groovy/dev-permissions.groovy', array(
                    '__JOB_NAME__' => $dest,
                ));
            }
        }

        return true;
    }

    public function runGroovy($tmpl, array $tokens = array())
    {
        $groovy = file_get_contents($tmpl);
        $groovy = str_replace(array_keys($tokens), $tokens, $groovy);

        $task = TaskFactory::create('DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Console\Jenkins', array(
            'settings' => $this->settingsService->getSettings($this->ciTypeBuilder->getType('jenkins')),
            'directory' => $this->jenkinsDir,
            'command' => 'groovy =',
            'pipe_in' => escapeshellarg($groovy),
        ));

        $result = $task->execute();

        if (!$result->isSuccess()) {
            throw new \RuntimeException('Failed to run groovy script for jenkins job');
        }

        return true;
    }

    private function prepareGroovyScript($script, $jobName)
    {
        $string = <<<EOD
import jenkins.model.*
import hudson.model.*
import hudson.security.AuthorizationMatrixProperty
def instance = Jenkins.getInstance()


def job = instance.getItem("$jobName")

$script

job.save()
EOD;

        return $string;
    }

    public function runGroovyScript($script, array $tokens = array(), $jobName)
    {
        $groovy = $this->prepareGroovyScript($script, $jobName);
        $groovy = str_replace(array_keys($tokens), $tokens, $groovy);

        $task = TaskFactory::create('DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Console\Jenkins', array(
            'settings' => $this->settingsService->getSettings($this->ciTypeBuilder->getType('jenkins')),
            'directory' => $this->jenkinsDir,
            'command' => 'groovy =',
            'pipe_in' => escapeshellarg($groovy),
        ));

        $result = $task->execute();

        if (!$result->isSuccess()) {
            throw new \RuntimeException('Failed to run groovy script for jenkins job');
        }

        return true;
    }

    /////// REFACTOR FROM ENVIRONMENTSERVICE
    ///
    //
}
