<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CoreBundle\Event\BuildEvent;
use DigipolisGent\Domainator9k\CoreBundle\Event\DestroyEvent;
use DigipolisGent\Domainator9k\CoreBundle\Service\TaskService;
use DigipolisGent\Domainator9k\CoreBundle\Service\TemplateService;
use DigipolisGent\SettingBundle\Service\DataValueService;
use GuzzleHttp\Exception\ClientException;

/**
 * Class DestroyEventListener
 *
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener
 */
class DestroyEventListener
{

    private $dataValueService;
    private $templateService;
    private $taskService;
    private $apiServiceFactory;

    public function __construct(
        DataValueService $dataValueService,
        TemplateService $templateService,
        TaskService $taskService,
        ApiServiceFactory $apiServiceFactory
    ) {
        $this->dataValueService = $dataValueService;
        $this->templateService = $templateService;
        $this->taskService = $taskService;
        $this->apiServiceFactory = $apiServiceFactory;
    }

    /**
     * @param BuildEvent $event
     */
    public function onDestroy(DestroyEvent $event)
    {
        $task = $event->getTask();
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

            $this->taskService
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
                        $this->taskService->addWarningLogMessage($task, 'Job not found.');
                        continue;
                    }

                    throw $ex;
                }

                // Remove it.
                $apiService->removeJob($jobName);

                $this->taskService->addSuccessLogMessage($task, 'Job removed.');
            } catch (ClientException $exception) {
                $this->taskService
                    ->addErrorLogMessage($task, $ex->getMessage())
                    ->addFailedLogMessage($task, 'Removal failed.');

                $event->stopPropagation();
            }
        }
    }
}
