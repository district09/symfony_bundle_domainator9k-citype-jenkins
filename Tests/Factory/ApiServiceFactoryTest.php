<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Factory;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory\ApiServiceFactory;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\ApiService;
use PHPUnit\Framework\TestCase;

class ApiServiceFactoryTest extends TestCase
{

    public function testCreate()
    {
        $factory = new ApiServiceFactory();
        $jenkinsServer = new JenkinsServer();
        $this->assertInstanceOf(ApiService::class, $factory->create($jenkinsServer));
    }
}
