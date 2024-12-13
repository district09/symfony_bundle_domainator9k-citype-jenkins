<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Provisioner;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Provisioner\BuildProvisioner;
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

class BuildProvisionerTest extends TestCase
{

    public function testOnBuildWithException()
    {
        $this->expectException(LoggedException::class);

        $dataValueService = $this->getDataValueServiceMock();
        $templateService = $this->getTemplateServiceMock();
        $taskLoggerService = $this->getTaskLoggerService();
        $apiService = $this->getApiServiceMock();
        $apiServiceFactory = $this->getApiServiceFactoryMock($apiService);
        $applicationEnvironment = new ApplicationEnvironment();

        $groovyScripts = new ArrayCollection();
        $groovyScripts->add(new JenkinsGroovyScript());

        $jenkinsJob = new JenkinsJob();
        $jenkinsJob->setJenkinsGroovyScripts($groovyScripts);

        $jenkinsJobs = new ArrayCollection();
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


        $templateService
            ->expects($this->atLeastOnce())
            ->method('replaceKeys')
            ->willReturn('my_job_name');

        $apiService
            ->expects($this->atLeastOnce())
            ->method('executeGroovyScript')
            ->willReturnCallback(function () {
                $exception = new ClientException('This is an exception.',
                    $this->getRequestMock(),
                    $this->getResponseMock()
                );

                throw $exception;
            });

        $task = new Task();
        $task->setType(Task::TYPE_BUILD);
        $task->setApplicationEnvironment($applicationEnvironment);

        $provisioner = new BuildProvisioner(
            $dataValueService,
            $templateService,
            $taskLoggerService,
            $apiServiceFactory
        );
        $provisioner->setTask($task);
        $provisioner->run();
    }

    public function testOnBuildWithoutException()
    {
        $dataValueService = $this->getDataValueServiceMock();
        $templateService = $this->getTemplateServiceMock();
        $taskLoggerService = $this->getTaskLoggerService();
        $apiService = $this->getApiServiceMock();
        $apiServiceFactory = $this->getApiServiceFactoryMock($apiService);
        $applicationEnvironment = new ApplicationEnvironment();


        $groovyScripts = new ArrayCollection();
        $groovyScripts->add(new JenkinsGroovyScript());

        $jenkinsJob = new JenkinsJob();
        $jenkinsJob->setJenkinsGroovyScripts($groovyScripts);

        $jenkinsJobs = new ArrayCollection();
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


        $templateService
            ->expects($this->atLeastOnce())
            ->method('replaceKeys')
            ->willReturn('my_job_name');

        $apiService
            ->expects($this->atLeastOnce())
            ->method('executeGroovyScript')
            ->willReturn(NULL);

        $task = new Task();
        $task->setType(Task::TYPE_BUILD);
        $task->setApplicationEnvironment($applicationEnvironment);

        $provisioner = new BuildProvisioner(
            $dataValueService,
            $templateService,
            $taskLoggerService,
            $apiServiceFactory
        );
        $provisioner->setTask($task);
        $provisioner->run();
    }

    private function getApiServiceMock()
    {
        $mock = $this
            ->getMockBuilder(ApiService::class)
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

    private function getTaskLoggerService()
    {
        $mock = $this
            ->getMockBuilder(TaskLoggerService::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function getRequestMock()
    {
        $mock = $this
            ->getMockBuilder(RequestInterface::class)
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
