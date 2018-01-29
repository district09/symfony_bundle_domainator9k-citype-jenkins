<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Service;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\ApiService;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ApiServiceTest extends TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetJobWithException()
    {
        $apiService = $this->getApiServiceMock();
        $apiService->getJob('job-name');
    }

    public function testGetJob()
    {
        $client = $this->getClientMock();

        $body = $this->getBodyMock([]);
        $reponse = $this->getResponseMock($body);

        $client
            ->expects($this->at(0))
            ->method('__call')
            ->willReturn($reponse);


        $apiService = $this->getApiServiceMock($client);
        $apiService->getJob('job-name');
    }

    public function testCreateJob()
    {
        $client = $this->getClientMock();
        $apiService = $this->getApiServiceMock($client);
        $result = $apiService->createJob('template-name','new-job-name');
        $this->assertNull($result);
    }

    public function testRemoveJob()
    {
        $client = $this->getClientMock();
        $apiService = $this->getApiServiceMock($client);
        $result = $apiService->removeJob('job-name');
        $this->assertNull($result);
    }

    public function testExecuteGroovyScript()
    {
        $client = $this->getClientMock();
        $apiService = $this->getApiServiceMock($client);
        $result = $apiService->executeGroovyscript('script');
        $this->assertNull($result);
    }

    private function getApiServiceMock($client = null)
    {
        $jenkinsServer = new JenkinsServer();
        $jenkinsServer->setName('example-name');
        $jenkinsServer->setPort(22);
        $jenkinsServer->setJenkinsUrl('example-url');
        $jenkinsServer->setToken('token');

        $service = new ApiService($jenkinsServer);

        $reflectionObject = new \ReflectionObject($service);
        $property = $reflectionObject->getProperty('client');
        $property->setAccessible(true);
        $property->setValue(
            null,
            $client
        );

        return $service;
    }

    private function getClientMock()
    {
        $mock = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function getBodyMock($result)
    {
        $mock = $this
            ->getMockBuilder(StreamInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects($this->at(0))
            ->method('getContents')
            ->willReturn(json_encode($result));

        return $mock;
    }

    private function getResponseMock($body)
    {
        $mock = $this
            ->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects($this->at(0))
            ->method('getBody')
            ->willReturn($body);

        return $mock;
    }
}

