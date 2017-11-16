<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsSettings;

class JenkinsTypeSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('jenkinsUrl', TextType::class)
            ->add('jenkinsCli', TextType::class)
            ->add('jenkinsPrivateKeyFile', TextType::class)
            ->add('jenkinsPrivateKeyPassphrase', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => JenkinsSettings::class,
            'deploy_type' => null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'jenkins_deploy_type_settings';
    }
}
