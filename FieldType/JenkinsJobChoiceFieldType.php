<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\GroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type\JenkinsJobFormType;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class JenkinsJobChoiceFieldType
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType
 */
class JenkinsJobChoiceFieldType extends AbstractFieldType
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
        return 'jenkins_job';
    }

    /**
     * @return string
     */
    public function getFormType(): string
    {
        return CollectionType::class;
    }

    /**
     * @param $value
     * @return array
     */
    public function getOptions($value): array
    {
        $groovyScripts = $this->entityManager->getRepository(JenkinsGroovyScript::class)->findAll();

        $options = [];
        $options['entry_type'] = JenkinsJobFormType::class;
        $options['allow_add'] = true;
        $options['allow_delete'] = true;
        $options['by_reference'] = false;
        $options['prototype'] = true;
        $options['prototype_data'] = new JenkinsJob();
        $options['entry_options']['groovy_script_options'] = $groovyScripts;

        $ids = json_decode($value, true);

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
                ->findOneByKey($applicationTypeEnvironment, self::getName());

            if (!is_null($settingDataValue)) {
                $ids = json_decode($settingDataValue->getValue(), true);
            }
        }

        $jenkinsJobRepository = $this->entityManager->getRepository(JenkinsJob::class);

        $data = [];

        if (!is_null($ids)) {
            foreach ($ids as $id) {
                $data[] = $jenkinsJobRepository->find($id);
            }
        }

        $options['data'] = $data;

        return $options;
    }

    /**
     * @param $value
     * @return string
     */
    public function encodeValue($value): string
    {
        $jenkinsJobIds = [];

        foreach ($value as $jenkinsJob) {
            $this->entityManager->persist($jenkinsJob);
            $jenkinsJobIds[] = $jenkinsJob->getId();
        }

        return json_encode($jenkinsJobIds);
    }

    /**
     * @param $value
     * @return array
     */
    public function decodeValue($value)
    {
        $ids = json_decode($value, true);
        $jenkinsJobRepository = $this->entityManager->getRepository(JenkinsJob::class);
        $jenkinsJobs = [];

        if (!is_null($ids)) {
            foreach ($ids as $id) {
                $jenkinsJobs[] = $jenkinsJobRepository->find($id);
            }
        }

        return $jenkinsJobs;
    }

}