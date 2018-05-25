<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\EventListener;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener\DestroyEventListener;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\ApiService;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationEnvironment;
use DigipolisGent\Domainator9k\CoreBundle\Entity\Task;
use DigipolisGent\Domainator9k\CoreBundle\Event\DestroyEvent;
use DigipolisGent\Domainator9k\CoreBundle\Service\TaskService;
use DigipolisGent\Domainator9k\CoreBundle\Service\TemplateService;
use DigipolisGent\SettingBundle\Service\DataValueService;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DestroyEventListenerTest extends TestCase
{

    public function testOnDestroyWithException()
    {
        $dataValueService = $this->getDataValueServiceMock();
        $templateService = $this->getTemplateServiceMock();
        $taskService = $this->getTaskServiceMock();
        $apiService = $this->getApiServiceMock();
        $apiServiceFactory = $this->getApiServiceFactoryMock($apiService);

        $jenkinsServer = new JenkinsServer();

        $dataValueService
            ->expects($this->at(0))
            ->method('getValue')
            ->willReturn($jenkinsServer);

        $jenkinsJobs = new ArrayCollection();

        $jenkinsJob = new JenkinsJob();
        $jenkinsJobs->add($jenkinsJob);

        $dataValueService
            ->expects($this->at(1))
            ->method('getValue')
            ->willReturn($jenkinsJobs);

        $apiService
            ->expects($this->at(0))
            ->method('getJob')
            ->willReturnCallback(function () {
                $exception = new ClientException('This is an exception.',
                    $this->getRequestMock(),
                    $this->getResponseMock()
                );

                throw $exception;
            });

        $applicationEnvironment = new ApplicationEnvironment();
        $task = new Task();
        $task->setType(Task::TYPE_DESTROY);
        $task->setApplicationEnvironment($applicationEnvironment);

        $destroyEvent = new DestroyEvent($task);

        $eventListener = new DestroyEventListener(
            $dataValueService,
            $templateService,
            $taskService,
            $apiServiceFactory
        );
        $eventListener->onDestroy($destroyEvent);
    }

    public function testOnDestroyWithoutException()
    {
        $dataValueService = $this->getDataValueServiceMock();
        $templateService = $this->getTemplateServiceMock();
        $taskService = $this->getTaskServiceMock();
        $apiService = $this->getApiServiceMock();
        $apiServiceFactory = $this->getApiServiceFactoryMock($apiService);

        $jenkinsServer = new JenkinsServer();

        $dataValueService
            ->expects($this->at(0))
            ->method('getValue')
            ->willReturn($jenkinsServer);

        $jenkinsJobs = new ArrayCollection();

        $jenkinsJob = new JenkinsJob();
        $jenkinsJobs->add($jenkinsJob);

        $dataValueService
            ->expects($this->at(1))
            ->method('getValue')
            ->willReturn($jenkinsJobs);

        $apiService
            ->expects($this->at(0))
            ->method('getJob');

        $apiService
            ->expects($this->at(1))
            ->method('removeJob');

        $applicationEnvironment = new ApplicationEnvironment();
        $task = new Task();
        $task->setType(Task::TYPE_DESTROY);
        $task->setApplicationEnvironment($applicationEnvironment);

        $destroyEvent = new DestroyEvent($task);

        $eventListener = new DestroyEventListener(
            $dataValueService,
            $templateService,
            $taskService,
            $apiServiceFactory
        );
        $eventListener->onDestroy($destroyEvent);
    }

    private function getRequestMock()
    {
        $mock = $this
            ->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function getDataValueServiceMock()
    {
        $mock = $this
            ->getMockBuilder(DataValueService::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function getTemplateServiceMock()
    {
        $mock = $this
            ->getMockBuilder(TemplateService::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function getTaskServiceMock()
    {
        $mock = $this
            ->getMockBuilder(TaskService::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function getApiServiceFactoryMock($apiService)
    {
        $mock = $this
            ->getMockBuilder(ApiServiceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects($this->at(0))
            ->method('create')
            ->willReturn($apiService);

        return $mock;
    }

    private function getApiServiceMock()
    {
        $mock = $this
            ->getMockBuilder(ApiService::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function getResponseMock()
    {
        $mock = $this
            ->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->method('getStatusCode')
            ->willReturn(404);

        return $mock;
    }
}
