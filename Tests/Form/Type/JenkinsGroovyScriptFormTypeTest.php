<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Form\Type;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type\JenkinsGroovyScriptFormType;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type\JenkinsJobFormType;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type\JenkinsServerFormType;

class JenkinsGroovyScriptFormTypeTest extends AbstractFormTypeTest
{

    public function testConfigureOptions()
    {
        $optionsResolver = $this->getOptionsResolverMock();

        $optionsResolver
            ->expects($this->at(0))
            ->method('setDefault')
            ->with('data_class',JenkinsGroovyScript::class);

        $formType = new JenkinsGroovyScriptFormType();
        $formType->configureOptions($optionsResolver);
    }

    public function testBuildForm()
    {
        $formBuilder = $this->getFormBuilderMock();

        $arguments = [
            'name',
            'content',
        ];

        $index = 0;

        foreach ($arguments as $argument) {
            $formBuilder
                ->expects($this->at($index))
                ->method('add')
                ->with($argument);

            $index++;
        }

        $formType = new JenkinsGroovyScriptFormType();
        $formType->buildForm($formBuilder,[]);
    }
}
