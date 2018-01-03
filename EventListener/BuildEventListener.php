<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener;


use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\ApiService;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\TemplateService;
use DigipolisGent\Domainator9k\CoreBundle\Event\BuildEvent;
use DigipolisGent\Domainator9k\CoreBundle\Service\TaskLoggerService;
use DigipolisGent\SettingBundle\Service\DataValueService;
use GuzzleHttp\Exception\ClientException;

/**
 * Class BuildEventListener
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener
 */
class BuildEventListener
{

    private $dataValueService;
    private $templateService;
    private $taskLoggerService;

    public function __construct(
        DataValueService $dataValueService,
        TemplateService $templateService,
        TaskLoggerService $taskLoggerService
    ) {
        $this->dataValueService = $dataValueService;
        $this->templateService = $templateService;
        $this->taskLoggerService = $taskLoggerService;
    }

    /**
     * @param BuildEvent $event
     */
    public function onBuild(BuildEvent $event)
    {
        $applicationEnvironment = $event->getTask()->getApplicationEnvironment();

        /** @var JenkinsServer $jenkinsServer */
        $jenkinsServer = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_server');
        $apiService = new ApiService($jenkinsServer);

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

            // Check if a job with this name allready exists, create one if not
            try {
                $this->taskLoggerService->addLine(
                    sprintf(
                        'Looking for jenkings job "%s"',
                        $jobName
                    )
                );
                $apiService->getJob($jobName);
            } catch (ClientException $exception) {
                if ($exception->getCode() == 404) {
                    $this->taskLoggerService->addLine(
                        sprintf(
                            'Creating jenkins job "%s"',
                            $jobName
                        )
                    );
                    $apiService->createJob($jenkinsServer->getTemplateName(), $jobName);
                }

                $this->taskLoggerService->addLine(
                    sprintf(
                        'Error on updating jenkins with message "%s"',
                        $exception->getMessage()
                    )
                );
            }

            // Execute all groovy scripts after replacing the tokens with the actual values
            /** @var JenkinsGroovyScript $jenkinsGroovyScript */
            foreach ($jenkinsJob->getJenkinsGroovyScripts() as $jenkinsGroovyScript) {
                $script = $this->templateService->replaceKeys(
                    $jenkinsGroovyScript->getContent(),
                    [
                        'jenkins_job' => $jenkinsJob,
                    ]
                );

                $script = $this->templateService->replaceKeys(
                    $script,
                    [
                        'application' => $applicationEnvironment->getApplication(),
                        'application_environment' => $applicationEnvironment,
                    ]
                );

                try {
                    $this->taskLoggerService->addLine(
                        sprintf(
                            'Executing groovy script "%s"',
                            $jenkinsGroovyScript->getName()
                        )
                    );
                    $apiService->executeGroovyscript($script);
                } catch (ClientException $exception) {
                    $this->taskLoggerService->addLine(
                        sprintf(
                            'Error on updating jenkins with message "%s"',
                            $exception->getMessage()
                        )
                    );
                }
            }
        }
    }
}