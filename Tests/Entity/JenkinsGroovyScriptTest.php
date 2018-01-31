<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Entity;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use PHPUnit\Framework\TestCase;

class JenkinsGroovyScriptTest extends TestCase
{

    public function testGettersAndSetters()
    {
        $groovyScript = new JenkinsGroovyScript();
        $groovyScript->setName('my-name');
        $groovyScript->setContent('my-content');
        $this->assertEquals('my-name',$groovyScript->getName());
        $this->assertEquals('my-content',$groovyScript->getContent());
    }
}
