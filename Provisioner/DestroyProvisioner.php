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
        $applicationEnvironment = $task->getApplicationEnvironment();

        /** @var JenkinsServer $jenkinsServer */
        $jenkinsServer = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_server');

        /** @var JenkinsJob[] $jenkinsJobs */
        $jenkinsJobs = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_job');

        $apiService = $this->apiServiceFactory->create($jenkinsServer);

        foreach ($jenkinsJobs as $jenkinsJob) {
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
                    $task,
                    sprintf(
                        'Removing Jenkins job "%s"',
                        $jobName
                    ),
                    0,
                    true
                );

            try {
                // Check if the job exists.
                try {
                    $apiService->getJob($jobName);
                } catch (ClientException $ex) {
                    if ($ex->getCode() == 404) {
                        $this->taskLoggerService->addWarningLogMessage($task, 'Job not found.');
                        continue;
                    }

                    throw $ex;
                }

                // Remove it.
                $apiService->removeJob($jobName);

                $this->taskLoggerService->addSuccessLogMessage($task, 'Job removed.');
            } catch (ClientException $exception) {
                $this->taskLoggerService
                    ->addErrorLogMessage($task, $ex->getMessage())
                    ->addFailedLogMessage($task, 'Removal failed.');

                $task->setFailed();
            }
        }
    }

    public function getName()
    {
        return 'Jenkins jobs';
    }
}
