<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Provisioner;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CoreBundle\Entity\Task;
use DigipolisGent\Domainator9k\CoreBundle\Provisioner\ProvisionerInterface;
use DigipolisGent\Domainator9k\CoreBundle\Service\TaskService;
use DigipolisGent\Domainator9k\CoreBundle\Service\TemplateService;
use DigipolisGent\SettingBundle\Service\DataValueService;
use GuzzleHttp\Exception\ClientException;

/**
 * Class BuildProvisioner
 *
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Provisioner
 */
class BuildProvisioner implements ProvisionerInterface
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

                    $task->setFailed();
                    return;
                }
            }
        }
    }
}
