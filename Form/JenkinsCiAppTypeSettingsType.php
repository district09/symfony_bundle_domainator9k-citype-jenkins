<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\GroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsCiAppTypeSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JenkinsCiAppTypeSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ciAppTypeSettings = $builder->getData();
        if (!$ciAppTypeSettings->getDeployJobTemplate() || $ciAppTypeSettings->getDeployJobTemplate() === '') {
            $ciAppTypeSettings->setDeployJobTemplate($ciAppTypeSettings->getAdditionalConfig()['templates']['deploy']);
        }
        if (!$ciAppTypeSettings->getRevertJobTemplate() || $ciAppTypeSettings->getRevertJobTemplate() === '') {
            $ciAppTypeSettings->setRevertJobTemplate($ciAppTypeSettings->getAdditionalConfig()['templates']['revert']);
        }
        if (!$ciAppTypeSettings->getSyncJobTemplate() || $ciAppTypeSettings->getSyncJobTemplate() === '') {
            $ciAppTypeSettings->setSyncJobTemplate($ciAppTypeSettings->getAdditionalConfig()['templates']['sync']);
        }
        if (!$ciAppTypeSettings->getDumpJobTemplate() || $ciAppTypeSettings->getDumpJobTemplate() === '') {
            $ciAppTypeSettings->setDumpJobTemplate($ciAppTypeSettings->getAdditionalConfig()['templates']['dump']);
        }
        if (!$ciAppTypeSettings->getValidateJobTemplate() || $ciAppTypeSettings->getValidateJobTemplate() === '') {
            $ciAppTypeSettings->setValidateJobTemplate($ciAppTypeSettings->getAdditionalConfig()['templates']['validate']);
        }

        if (count($ciAppTypeSettings->getDeployJobGroovyScripts()) === 0) {
            $script = new GroovyScript();
            $script->setContent($ciAppTypeSettings->getAdditionalConfig()['groovyscripts']['deploy']);
            $ciAppTypeSettings->addDeployJobGroovyScript($script);
        }

        if (count($ciAppTypeSettings->getRevertJobGroovyScripts()) === 0) {
            $script = new GroovyScript();
            $script->setContent($ciAppTypeSettings->getAdditionalConfig()['groovyscripts']['revert']);
            $ciAppTypeSettings->addRevertJobGroovyScript($script);
        }

        if (count($ciAppTypeSettings->getSyncJobGroovyScripts()) === 0) {
            $script = new GroovyScript();
            $script->setContent($ciAppTypeSettings->getAdditionalConfig()['groovyscripts']['sync']);
            $ciAppTypeSettings->addSyncJobGroovyScript($script);
        }

        if (count($ciAppTypeSettings->getDumpJobGroovyScripts()) === 0) {
            $script = new GroovyScript();
            $script->setContent($ciAppTypeSettings->getAdditionalConfig()['groovyscripts']['dump']);
            $ciAppTypeSettings->addDumpJobGroovyScript($script);
        }

        if (count($ciAppTypeSettings->getValidateJobGroovyScripts()) === 0) {
            $script = new GroovyScript();
            $script->setContent($ciAppTypeSettings->getAdditionalConfig()['groovyscripts']['validate']);
            $ciAppTypeSettings->addValidateJobGroovyScript($script);
        }

        $builder

            // move this to seperate form in basedeploystuff somehow
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->add('deployJobEnabled', CheckboxType::class, ['required' => false])
            ->add('deployJobTemplate', TextType::class, ['required' => true])
            ->add('deployJobGroovyScripts', CollectionType::class, [
                'entry_type' => GroovyScriptType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ])
            ->add('revertJobEnabled', CheckboxType::class, ['required' => false])
            ->add('revertJobTemplate', TextType::class, ['required' => true])
            ->add('revertJobGroovyScripts', CollectionType::class, [
                'entry_type' => GroovyScriptType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ])
            ->add('syncJobEnabled', CheckboxType::class, ['required' => false])
            ->add('syncJobTemplate', TextType::class, ['required' => true])
            ->add('syncJobGroovyScripts', CollectionType::class, [
                'entry_type' => GroovyScriptType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ])
            ->add('dumpJobEnabled', CheckboxType::class, ['required' => false])
            ->add('dumpJobTemplate', TextType::class, ['required' => true])
            ->add('dumpJobGroovyScripts', CollectionType::class, [
                'entry_type' => GroovyScriptType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ])
            ->add('validateJobEnabled', CheckboxType::class, ['required' => false])
            ->add('validateJobTemplate', TextType::class, ['required' => true])
            ->add('validateJobGroovyScripts', CollectionType::class, [
                'entry_type' => GroovyScriptType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => JenkinsCiAppTypeSettings::class,
        ));
    }

    public function getBlockPrefix()
    {
        return 'jenkins_ci_app_type_settings';
    }
}
