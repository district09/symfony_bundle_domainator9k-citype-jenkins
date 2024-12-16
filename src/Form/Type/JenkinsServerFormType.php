<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JenkinsServerFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('name');
        $builder->add('url');
        $builder->add('port');
        $builder->add('user');
        $builder->add('token');
        $builder->add('csrfProtected');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', JenkinsServer::class);
    }
}
