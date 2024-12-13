<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Provisioner;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Provisioner\DestroyProvisioner;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\ApiService;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationEnvironment;
use DigipolisGent\Domainator9k\CoreBundle\Entity\Task;
use DigipolisGent\Domainator9k\CoreBundle\Exception\LoggedException;
use DigipolisGent\Domainator9k\CoreBundle\Service\TaskLoggerService;
use DigipolisGent\Domainator9k\CoreBundle\Service\TemplateService;
use DigipolisGent\SettingBundle\Service\DataValueService;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DestroyProvisionerTest extends TestCase
{

    public function testOnDestroyWithException()
    {
        $this->expectException(LoggedException::class);
        $dataValueService = $this->getDataValueServiceMock();
        $templateService = $this->getTemplateServiceMock();
        $taskLoggerService = $this->getTaskLoggerServiceMock();
        $apiService = $this->getApiServiceMock();
        $apiServiceFactory = $this->getApiServiceFactoryMock($apiService);
        $applicationEnvironment = new ApplicationEnvironment();

        $jenkinsServer = new JenkinsServer();

        $jenkinsJobs = new ArrayCollection();

        $jenkinsJob = new JenkinsJob();
        $jenkinsJobs->add($jenkinsJob);

        $dataValueService
            ->expects($this->atLeast(2))
            ->method('getValue')
            ->willReturnCallback(function(ApplicationEnvironment $appEnv, string $key) use ($applicationEnvironment, $jenkinsJobs) {
                $this->assertSame($applicationEnvironment, $appEnv);
                switch ($key) {
                  case 'jenkins_server':
                    return new JenkinsServer();

                  case 'jenkins_job':
                    return $jenkinsJobs;
                }

                $this->fail('This line should not be reached');
            });

        $apiService
            ->expects($this->atLeastOnce())
            ->method('getJob')
            ->willReturnCallback(function () {
                $exception = new ClientException('This is an exception.',
                    $this->getRequestMock(),
                    $this->getResponseMock()
                );

                throw $exception;
            });

        $task = new Task();
        $task->setType(Task::TYPE_DESTROY);
        $task->setApplicationEnvironment($applicationEnvironment);

        $provisioner = new DestroyProvisioner(
            $dataValueService,
            $templateService,
            $taskLoggerService,
            $apiServiceFactory
        );
        $provisioner->setTask($task);
        $provisioner->run();
    }

    public function testOnDestroyWithoutException()
    {
        $dataValueService = $this->getDataValueServiceMock();
        $templateService = $this->getTemplateServiceMock();
        $taskLoggerService = $this->getTaskLoggerServiceMock();
        $apiService = $this->getApiServiceMock();
        $apiServiceFactory = $this->getApiServiceFactoryMock($apiService);
        $applicationEnvironment = new ApplicationEnvironment();

        $jenkinsServer = new JenkinsServer();

        $jenkinsJobs = new ArrayCollection();

        $jenkinsJob = new JenkinsJob();
        $jenkinsJobs->add($jenkinsJob);

        $dataValueService
            ->expects($this->atLeast(2))
            ->method('getValue')
            ->willReturnCallback(function(ApplicationEnvironment $appEnv, string $key) use ($applicationEnvironment, $jenkinsJobs) {
                $this->assertSame($applicationEnvironment, $appEnv);
                switch ($key) {
                  case 'jenkins_server':
                    return new JenkinsServer();

                  case 'jenkins_job':
                    return $jenkinsJobs;
                }

                $this->fail('This line should not be reached');
            });

        $apiService
            ->expects($this->atLeastOnce())
            ->method('getJob');

        $apiService
            ->expects($this->atLeastOnce())
            ->method('removeJob');

        $task = new Task();
        $task->setType(Task::TYPE_DESTROY);
        $task->setApplicationEnvironment($applicationEnvironment);

        $provisioner = new DestroyProvisioner(
            $dataValueService,
            $templateService,
            $taskLoggerService,
            $apiServiceFactory
        );
        $provisioner->setTask($task);
        $provisioner->run();
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

    private function getTaskLoggerServiceMock()
    {
        $mock = $this
            ->getMockBuilder(TaskLoggerService::class)
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
            ->expects($this->atLeastOnce())
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
