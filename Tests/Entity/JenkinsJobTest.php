<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Entity;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use PHPUnit\Framework\TestCase;

class JenkinsJobTest extends TestCase
{

    public function testGettersAndSetters()
    {
        $jenkinsJob = new JenkinsJob();
        $jenkinsJob->setName('my-name');
        $jenkinsJob->setSystemName('my-system-name');
        $this->assertEquals('my-name', $jenkinsJob->getName());
        $this->assertEquals('my-system-name', $jenkinsJob->getSystemName());
    }

    public function testGetTemplateReplacements()
    {
        $expected = [
            'systemName()' => 'getSystemName()',
        ];
        $this->assertEquals($expected, JenkinsJob::getTemplateReplacements());
    }
}
