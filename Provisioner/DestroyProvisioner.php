<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Provisioner;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CoreBundle\Entity\Task;
use DigipolisGent\Domainator9k\CoreBundle\Provisioner\ProvisionerInterface;
use DigipolisGent\Domainator9k\CoreBundle\Service\TaskLoggerService;
use DigipolisGent\Domainator9k\CoreBundle\Service\TemplateService;
use DigipolisGent\SettingBundle\Service\DataValueService;
use GuzzleHttp\Exception\ClientException;

/**
 * Class DestroyProvisioner
 *
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Provisioner
 */
class DestroyProvisioner implements ProvisionerInterface
{

    private $dataValueService;
    private $templateService;
    private $taskLoggerService;
    private $apiServiceFactory;

    public function __construct(
        DataValueService $dataValueService,
        TemplateService $templateService,
        TaskLoggerService $taskLoggerService,
        ApiServiceFactory $apiServiceFactory
    ) {
        $this->dataValueService = $dataValueService;
        $this->templateService = $templateService;
        $this->taskLoggerService = $taskLoggerService;
        $this->apiServiceFactory = $apiServiceFactory;
    }

    /**
     * @param Task $task
     */
    public function run(Task $task)
    {
        $this->task = $task;
        $applicationEnvironment = $this->task->getApplicationEnvironment();

        /** @var JenkinsServer $jenkinsServer */
        $jenkinsServer = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_server');

        /** @var JenkinsJob[] $jenkinsJobs */
        $jenkinsJobs = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_job');

        $this->apiService = $this->apiServiceFactory->create($jenkinsServer);

        foreach ($jenkinsJobs as $jenkinsJob) {
            try {
                $this->removeJenkinsJob($jenkinsJob);
            } catch (ClientException $exception) {
                $this->taskLoggerService
                    ->addErrorLogMessage($this->task, $exception->getMessage())
                    ->addFailedLogMessage($this->task, 'Removal failed.');

                $this->task->setFailed();
                return;
            }
        }
    }

    protected function removeJenkinsJob(JenkinsJob $jenkinsJob)
    {
        $applicationEnvironment = $this->task->getApplicationEnvironment();
        // Replace all tokens in the job name.
        $jobName = $this->templateService->replaceKeys(
            $jenkinsJob->getSystemName(),
            [
                'application' => $applicationEnvironment->getApplication(),
                'application_environment' => $applicationEnvironment
            ]
        );

        $this->taskLoggerService
            ->addLogHeader(
                $this->task,
                sprintf(
                    'Removing Jenkins job "%s"',
                    $jobName
                ),
                0,
                true
            );

        // Check if the job exists.
        try {
            $this->apiService->getJob($jobName);
        } catch (ClientException $ex) {
            if ($ex->getCode() == 404) {
                $this->taskLoggerService->addWarningLogMessage($this->task, 'Job not found.');
                return;
            }

            throw $ex;
        }

        // Remove it.
        $this->apiService->removeJob($jobName);
        $this->taskLoggerService->addSuccessLogMessage($this->task, 'Job removed.');
    }

    public function getName()
    {
        return 'Jenkins jobs';
    }
}
