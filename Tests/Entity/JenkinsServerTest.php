<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Entity;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use PHPUnit\Framework\TestCase;

class JenkinsServerTest extends TestCase
{

    public function testGettersAndSetters()
    {
        $jenkinsServer = new JenkinsServer();
        $jenkinsServer->setJenkinsUrl('my-jenkins-url');
        $jenkinsServer->setUrl('my-url');
        $jenkinsServer->setUser('my-user');
        $jenkinsServer->setTemplateName('my-template-name');

        $this->assertEquals('my-jenkins-url', $jenkinsServer->getJenkinsUrl());
        $this->assertEquals('my-url', $jenkinsServer->getUrl());
        $this->assertEquals('my-user', $jenkinsServer->getUser());
        $this->assertEquals('my-template-name', $jenkinsServer->getTemplateName());
    }
}

