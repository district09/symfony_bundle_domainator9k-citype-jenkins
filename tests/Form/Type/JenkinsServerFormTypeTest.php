<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Form\Type;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type\JenkinsServerFormType;

class JenkinsServerFormTypeTest extends AbstractFormTypeTest
{

    public function testConfigureOptions()
    {
        $optionsResolver = $this->getOptionsResolverMock();

        $optionsResolver
            ->expects($this->atLeastOnce())
            ->method('setDefault')
            ->with('data_class', JenkinsServer::class);

        $formType = new JenkinsServerFormType();
        $formType->configureOptions($optionsResolver);
    }

    public function testBuildForm()
    {
        $formBuilder = $this->getFormBuilderMock();

        $arguments = [
            'name',
            'url',
            'port',
            'user',
            'token',
            'csrfProtected',
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

        $formType = new JenkinsServerFormType();
        $formType->buildForm($formBuilder, []);
    }
}
