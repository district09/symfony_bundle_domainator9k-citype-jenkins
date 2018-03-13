<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\GroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\Type\JenkinsJobFormType;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationEnvironment;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationType;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationTypeEnvironment;
use DigipolisGent\SettingBundle\Entity\SettingDataValue;
use DigipolisGent\SettingBundle\FieldType\AbstractFieldType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
        $ateRepository = $this->entityManager->getRepository(ApplicationTypeEnvironment::class);
        $sdvRepository = $this->entityManager->getRepository(SettingDataValue::class);
        $groovyScriptRepository = $this->entityManager->getRepository(JenkinsGroovyScript::class);
        $atRepository = $this->entityManager->getRepository(ApplicationType::class);

        $options = [];
        $options['entry_type'] = JenkinsJobFormType::class;
        $options['allow_add'] = true;
        $options['allow_delete'] = true;
        $options['by_reference'] = false;
        $options['prototype'] = true;
        $options['prototype_data'] = new JenkinsJob();
        $options['entry_options']['groovy_script_options'] = $groovyScriptRepository->findAll();

        $data = $this->decodeValue($value);

        $originEntity = $this->getOriginEntity();

        if ($originEntity instanceof ApplicationEnvironment && is_null($originEntity->getId())) {
            $applicationType = $atRepository->findOneBy(
                ['name' => $originEntity->getApplication()->getApplicationType()]
            );

            $criteria = [
                'applicationType' => $applicationType,
                'environment' => $originEntity->getEnvironment(),
            ];

            $applicationTypeEnvironment = $ateRepository->findOneBy($criteria);

            $settingDataValue = $sdvRepository->findOneByKey($applicationTypeEnvironment, self::getName());

            if (!is_null($settingDataValue)) {
                $jenkinsJobs = $this->decodeValue($settingDataValue->getValue());
                foreach ($jenkinsJobs as $jenkinsJob) {
                    $data[] = clone $jenkinsJob;
                }
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
