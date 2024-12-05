<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Form\Type;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type\JenkinsJobFormType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JenkinsJobFormTypeTest extends AbstractFormTypeTest
{

    public function testConfigureOptions()
    {
        $optionsResolver = $this->getOptionsResolverMock();

        $optionsResolver
            ->expects($this->atLeastOnce())
            ->method('setRequired')
            ->with('groovy_script_options');

        $optionsResolver
            ->expects($this->atLeastOnce())
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
            'jenkinsGroovyScripts',
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

        $formType = new JenkinsJobFormType();
        $formType->buildForm($formBuilder,$options);
    }
}
