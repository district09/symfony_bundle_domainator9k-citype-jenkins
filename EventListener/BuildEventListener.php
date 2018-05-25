<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CoreBundle\Entity\Task;
use DigipolisGent\Domainator9k\CoreBundle\Event\BuildEvent;
use DigipolisGent\Domainator9k\CoreBundle\Service\TaskService;
use DigipolisGent\Domainator9k\CoreBundle\Service\TemplateService;
use DigipolisGent\SettingBundle\Service\DataValueService;
use GuzzleHttp\Exception\ClientException;

/**
 * Class BuildEventListener
 *
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener
 */
class BuildEventListener
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
    public function onBuild(BuildEvent $event)
    {
        $task = $event->getTask();
        $applicationEnvironment = $task->getApplicationEnvironment();

        /** @var JenkinsServer $jenkinsServer */
        $jenkinsServer = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_server');

        /** @var JenkinsJob[] $jenkinsJobs */
        $jenkinsJobs = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_job');

        $apiService = $this->apiServiceFactory->create($jenkinsServer);

        foreach ($jenkinsJobs as $jenkinsJob) {
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
                $this->taskService
                    ->addLogHeader(
                        $task,
                        sprintf(
                            'Executing Groovy script "%s"',
                            $jenkinsGroovyScript->getName()
                        )
                    )
                    ->addInfoLogMessage($task, $script);

                try {
                    $apiService->executeGroovyscript($script);

                    $this->taskService->addSuccessLogMessage($task, 'Execution succeeded.');
                } catch (ClientException $ex) {
                    $this->taskService
                        ->addErrorLogMessage($task, $ex->getMessage())
                        ->addFailedLogMessage($task, 'Execution failed.');

                    $event->stopPropagation();
                    return;
                }
            }
        }
    }
}
