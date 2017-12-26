<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\GroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CoreBundle\Entity\AbstractApplication;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationEnvironment;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationType;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationTypeEnvironment;
use DigipolisGent\SettingBundle\Entity\SettingDataValue;
use DigipolisGent\SettingBundle\FieldType\AbstractFieldType;
use DigipolisGent\SettingBundle\FieldType\FieldTypeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class JenkinsGroovyScriptChoiceFieldType
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType
 */
class JenkinsGroovyScriptChoiceFieldType extends AbstractFieldType
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
        return 'jenkins_groovy_script';
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

        $jenkinsGroovyScripts = $this->entityManager->getRepository(JenkinsGroovyScript::class)->findAll();
        foreach ($jenkinsGroovyScripts as $jenkinsGroovyScript) {
            $options['choices'][$jenkinsGroovyScript->getName()] = $jenkinsGroovyScript->getId();
        }

        $options['data'] = json_decode($value, true);

        $originEntity = $this->getOriginEntity();

        if ($originEntity instanceof ApplicationEnvironment && is_null($originEntity->getId())) {
            $applicationType = $this->entityManager->getRepository(ApplicationType::class)
                ->findOneBy(['type' => $originEntity->getApplication()->getType()]);

            $criteria = [
                'applicationType' => $applicationType,
                'environment' => $originEntity->getEnvironment(),
            ];

            $applicationTypeEnvironment = $this->entityManager
                ->getRepository(ApplicationTypeEnvironment::class)->findOneBy($criteria);

            $settingDataValue = $this->entityManager->getRepository(SettingDataValue::class)
                ->findOneByKey($applicationTypeEnvironment, 'jenkins_groovy_script');

            if ($settingDataValue) {
                $options['data'] = json_decode($settingDataValue->getValue(), true);
            }
        }

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