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
            ->expects($this->at(0))
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
        ];

        $index = 0;

        foreach ($arguments as $argument) {
            $formBuilder
                ->expects($this->at($index))
                ->method('add')
                ->with($argument);

            $index++;
        }

        $formType = new JenkinsServerFormType();
        $formType->buildForm($formBuilder, []);
    }
}
