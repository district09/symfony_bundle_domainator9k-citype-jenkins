<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type;


use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JenkinsJobFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('name');
        $builder->add('systemName');
        $builder->add('jenkinsGroovyScripts',
            EntityType::class,
            [
                'multiple' => true,
                'expanded' => true,
                'choices' => $options['groovy_script_options'],
                'choice_label' => 'name',
                'class' => JenkinsGroovyScript::class
            ]
        );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('groovy_script_options');

        $resolver->setDefaults([
            'data_class' => JenkinsJob::class,
        ]);
    }

}