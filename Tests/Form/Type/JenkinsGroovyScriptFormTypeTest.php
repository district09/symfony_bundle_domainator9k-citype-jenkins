<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Form\Type;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type\JenkinsGroovyScriptFormType;

class JenkinsGroovyScriptFormTypeTest extends AbstractFormTypeTest
{

    public function testConfigureOptions()
    {
        $optionsResolver = $this->getOptionsResolverMock();

        $optionsResolver
            ->expects($this->atLeastOnce())
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
            'order',
        ];

        $index = 0;

        $formBuilder->expects($this->atLeast(2))
            ->method('add')
            ->willReturnCallback(function ($argument) use ($arguments, &$index) {
                if (!array_key_exists($index, $arguments)) {
                    $this->fail('Did not expect invocation with argument ' . $argument . ' at invocation number ' . ($index + 1));
                }
                $this->assertEquals($arguments[$index], $argument);
                $index++;
            });

        $formType = new JenkinsGroovyScriptFormType();
        $formType->buildForm($formBuilder,[]);
    }
}
