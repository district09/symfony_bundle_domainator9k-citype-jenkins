<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CoreBundle\Event\BuildEvent;
use DigipolisGent\Domainator9k\CoreBundle\Service\TaskLoggerService;
use DigipolisGent\Domainator9k\CoreBundle\Service\TemplateService;
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
    public function onBuild(BuildEvent $event)
    {
        $applicationEnvironment = $event->getTask()->getApplicationEnvironment();

        $jenkinsServer = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_server');
        $apiService = $this->apiServiceFactory->create($jenkinsServer);

        $jenkinsJobs = $this->dataValueService->getValue($applicationEnvironment, 'jenkins_job');

        // Loop over all jenkins jobs
        /** @var JenkinsJob $jenkinsJob */
        foreach ($jenkinsJobs as $jenkinsJob) {
            // Execute all groovy scripts after replacing the tokens with the actual values
            /** @var JenkinsGroovyScripts $jenkinsGroovyScript[] */
            $jenkinsGroovyScripts = $jenkinsJob->getJenkinsGroovyScripts()
            usort($jenkinkGroovyScripts, function (JenkinsGroovyScript $a, JenkinsGroovyScript $b) {
                reutrn $a->getOrder() - $b->getOrder();
            });
            foreach ($jenkinsGroovyScripts as $jenkinsGroovyScript) {
                $script = $this->templateService->replaceKeys(
                    $jenkinsGroovyScript->getContent(),
                    [
                        'jenkins_job' => $jenkinsJob,
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
