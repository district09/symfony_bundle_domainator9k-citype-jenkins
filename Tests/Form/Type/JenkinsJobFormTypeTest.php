<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Form\Type;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type\JenkinsJobFormType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JenkinsJobFormTypeTest extends TestCase
{

    public function testConfigureOptions()
    {
        $optionsResolver = $this->getOptionsResolverMock();

        $optionsResolver
            ->expects($this->at(0))
            ->method('setRequired')
            ->with('groovy_script_options');

        $optionsResolver
            ->expects($this->at(1))
            ->method('setDefaults')
            ->with([ 'data_class' => JenkinsJob::class]);

        $formType = new JenkinsJobFormType();
        $formType->configureOptions($optionsResolver);
    }

    public function testBuildForm()
    {
        $formBuilder = $this->getFormBuilderMock();
        $options = [
            'groovy_script_options' => []
        ];

        $arguments = [
            'name',
            'systemName',
            'jenkinsGroovyScripts'
        ];

        $index = 0;

        foreach ($arguments as $argument){
            $formBuilder
                ->expects($this->at($index))
                ->method('add')
                ->with($argument);

            $index++;
        }

        $formType = new JenkinsJobFormType();
        $formType->buildForm($formBuilder,$options);
    }

    private function getFormBuilderMock()
    {
        $mock = $this
            ->getMockBuilder(FormBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function getOptionsResolverMock()
    {
        $mock = $this
            ->getMockBuilder(OptionsResolver::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }
}