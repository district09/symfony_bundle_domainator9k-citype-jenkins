<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CoreBundle\Event\BuildEvent;
use DigipolisGent\Domainator9k\CoreBundle\Event\DestroyEvent;
use DigipolisGent\Domainator9k\CoreBundle\Service\TaskLoggerService;
use DigipolisGent\Domainator9k\CoreBundle\Service\TemplateService;
use DigipolisGent\SettingBundle\Service\DataValueService;
use GuzzleHttp\Exception\ClientException;

/**
 * Class DestroyEventListener
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener
 */
class DestroyEventListener
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
     * @param BuildEvent $event
     */
    public function onDestroy(DestroyEvent $event)
    {
        $applicationEnvironment = $event->getTask()->getApplicationEnvironment();

        /** @var JenkinsServer $jenkinsServer */
        $jenkinsServer = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_server');
        $apiService = $this->apiServiceFactory->create($jenkinsServer);

        $jenkinsJobs = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_job');

        // Loop over all jenkins jobs
        /** @var JenkinsJob $jenkinsJob */
        foreach ($jenkinsJobs as $jenkinsJob) {
            // Replace all tokens in the jobname
            $jobName = $this->templateService->replaceKeys(
                $jenkinsJob->getSystemName(),
                [
                    'application' => $applicationEnvironment->getApplication(),
                    'application_environment' => $applicationEnvironment
                ]
            );

            // Check if a job with this name allready exists, delete it if it does
            try {
                $this->taskLoggerService->addLine(
                    sprintf(
                        'Looking for jenkins job "%s"',
                        $jobName
                    )
                );
                $apiService->getJob($jobName);
            } catch (ClientException $exception) {
                if ($exception->getCode() == 404) {
                    $this->taskLoggerService->addLine(
                        sprintf(
                            'Jenkins job "%s" not found',
                            $jobName
                        )
                    );

                    return;
                }
            }

            $this->taskLoggerService->addLine(
                sprintf(
                    'Removing jenkins job "%s"',
                    $jobName
                )
            );

            $apiService->removeJob($jobName);
        }
    }
}
