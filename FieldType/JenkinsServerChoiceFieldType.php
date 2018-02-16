<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\SettingBundle\FieldType\AbstractFieldType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class JenkinsServerChoiceFieldType
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType
 */
class JenkinsServerChoiceFieldType extends AbstractFieldType
{

    private $entityManager;

    /**
     * JenkinsServerChoiceFieldType constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'jenkins_server_choice';
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

        $jenkinsServers = $this->entityManager->getRepository(JenkinsServer::class)->findAll();
        foreach ($jenkinsServers as $jenkinsServer) {
            $options['choices'][$jenkinsServer->getName()] = $jenkinsServer->getId();

            if ($value == $jenkinsServer->getId()) {
                $options['data'] = $jenkinsServer->getId();
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
        return $value;
    }

    /**
     * @param $value
     * @return null|object
     */
    public function decodeValue($value)
    {
        return $this->entityManager->getRepository(JenkinsServer::class)->find($value);
    }
}
