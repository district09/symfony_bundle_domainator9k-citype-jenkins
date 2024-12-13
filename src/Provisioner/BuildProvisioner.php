<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Provisioner;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\ApiService;
use DigipolisGent\Domainator9k\CoreBundle\Exception\LoggedException;
use DigipolisGent\Domainator9k\CoreBundle\Provisioner\AbstractProvisioner;
use DigipolisGent\Domainator9k\CoreBundle\Service\TaskLoggerService;
use DigipolisGent\Domainator9k\CoreBundle\Service\TemplateService;
use DigipolisGent\SettingBundle\Service\DataValueService;
use GuzzleHttp\Exception\ClientException;

/**
 * Class BuildProvisioner
 *
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Provisioner
 */
class BuildProvisioner extends AbstractProvisioner
{

    protected ?ApiService $apiService;

    public function __construct(
        protected DataValueService $dataValueService,
        protected TemplateService $templateService,
        protected TaskLoggerService $taskLoggerService,
        protected ApiServiceFactory $apiServiceFactory,
    ) {
    }


    protected function doRun()
    {
        $applicationEnvironment = $this->task->getApplicationEnvironment();

        /** @var JenkinsServer $jenkinsServer */
        $jenkinsServer = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_server');

        /** @var JenkinsJob[] $jenkinsJobs */
        $jenkinsJobs = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_job');

        $this->apiService = $this->apiServiceFactory->create($jenkinsServer);

        foreach ($jenkinsJobs as $jenkinsJob) {
            try {
                $this->createJenkinsJob($jenkinsJob);
            } catch (ClientException $ex) {
                $this->taskLoggerService
                    ->addErrorLogMessage($this->task, $ex->getMessage())
                    ->addFailedLogMessage($this->task, 'Execution failed.');

                throw new LoggedException('', 0, $ex);
            }
        }
    }

    /**
     * @param JenkinsJob $jenkinsJob
     */
    protected function createJenkinsJob(JenkinsJob $jenkinsJob)
    {
        $applicationEnvironment = $this->task->getApplicationEnvironment();
        // Get and sort the Groovy scripts.
        $jenkinsGroovyScripts = $jenkinsJob->getJenkinsGroovyScripts();
        usort($jenkinsGroovyScripts, function (JenkinsGroovyScript $a, JenkinsGroovyScript $b) {
            return $a->getOrder() - $b->getOrder();
        });

        foreach ($jenkinsGroovyScripts as $jenkinsGroovyScript) {
            // Replace all tokens in the script.
            $script = $this->templateService->replaceKeys(
                $jenkinsGroovyScript->getContent(),
                [
                    'jenkins_job' => $jenkinsJob,
                    'application' => $applicationEnvironment->getApplication(),
                    'application_environment' => $applicationEnvironment,
                ]
            );

            // Execute the script.
            $this->taskLoggerService
                ->addLogHeader(
                    $this->task,
                    sprintf(
                        'Executing Groovy script "%s"',
                        $jenkinsGroovyScript->getName()
                    )
                )
                ->addInfoLogMessage($this->task, $script);

            $this->apiService->executeGroovyscript($script);
            $this->taskLoggerService->addSuccessLogMessage($this->task, 'Execution succeeded.');
        }
    }

    public function getName()
    {
        return 'Jenkins jobs';
    }
}
