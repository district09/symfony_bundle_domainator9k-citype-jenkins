<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\GroovyScript;
use DigipolisGent\SettingBundle\FieldType\FieldTypeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class GroovyScriptChoiceFieldType
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType
 */
class GroovyScriptChoiceFieldType implements FieldTypeInterface
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'groovy_script_choice';
    }

    /**
     * @return string
     */
    public function getFormType(): string
    {
        return ChoiceType::class;
    }

    /**
     * @param $value
     * @return array
     */
    public function getOptions($value): array
    {
        $options = [];

        $options['multiple'] = true;
        $options['expanded'] = true;

        $groovyScripts = $this->entityManager->getRepository(GroovyScript::class)->findAll();
        foreach ($groovyScripts as $groovyScript) {
            $options['choices'][$groovyScript->getName()] = $groovyScript->getId();
        }

        $options['data'] = json_decode($value,true);

        return $options;
    }

    /**
     * @param $value
     * @return string
     */
    public function encodeValue($value): string
    {
        return json_encode($value);
    }
}