<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\FieldType;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsGroovyScript;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsJob;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType\JenkinsJobChoiceFieldType;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\Fixtures\FooApplication;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationEnvironment;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationType;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationTypeEnvironment;
use DigipolisGent\SettingBundle\Entity\Repository\SettingDataValueRepository;
use DigipolisGent\SettingBundle\Entity\SettingDataValue;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class JenkinsJobChoiceFieldTypeTest extends TestCase
{

    public function testGetName()
    {
        $this->assertEquals('jenkins_job', JenkinsJobChoiceFieldType::getName());
    }

    public function testGetFormType()
    {
        $entityManager = $this->getEntityManagerMock();
        $fieldType = new JenkinsJobChoiceFieldType($entityManager);
        $this->assertEquals(CollectionType::class, $fieldType->getFormType());
    }


    public function testEncodeValue()
    {
        $entityManager = $this->getEntityManagerMock();

        $fieldType = new JenkinsJobChoiceFieldType($entityManager);

        $value = [
            $this->setEntitytId(new JenkinsJob(), 1),
            $this->setEntitytId(new JenkinsJob(), 2),
            $this->setEntitytId(new JenkinsJob(), 3),
        ];

        $result = $fieldType->encodeValue($value);
        $this->assertEquals('[1,2,3]', $result);
    }

    public function testDecodeValue()
    {
        $jenkinsJobRepository = $this->getRepositoryMock();

        for ($i = 1; $i <= 3; $i++) {
            $jenkinsJobRepository
                ->expects($this->at($i - 1))
                ->method('find')
                ->willReturn($this->setEntitytId(new JenkinsJob(), $i));
        }

        $repositories = [
            [
                'class' => JenkinsJob::class,
                'repository' => $jenkinsJobRepository,
            ]
        ];

        $entityManager = $this->getEntityManagerMock($repositories);

        $fieldType = new JenkinsJobChoiceFieldType($entityManager);

        $value = '[1,2,3]';
        $result = $fieldType->decodeValue($value);

        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
    }

    public function testGetOptionsForApplicationTypeEnvironment()
    {
        $ateRepository = $this->getRepositoryMock();
        $sdvRepository = $this->getRepositoryMock();
        $groovyScriptRepository = $this->getRepositoryMock();
        $atRepository = $this->getRepositoryMock();

        $repositories = [
            [
                'class' => ApplicationTypeEnvironment::class,
                'repository' => $ateRepository
            ],
            [
                'class' => SettingDataValue::class,
                'repository' => $sdvRepository
            ],
            [
                'class' => JenkinsGroovyScript::class,
                'repository' => $groovyScriptRepository
            ],
            [
                'class' => ApplicationType::class,
                'repository' => $atRepository
            ],
        ];

        $entityManager = $this->getEntityManagerMock($repositories);

        $applicationTypeEnvironment = new ApplicationTypeEnvironment();

        $fieldType = new JenkinsJobChoiceFieldType($entityManager);
        $fieldType->setOriginEntity($applicationTypeEnvironment);

        $options = $fieldType->getOptions('');
        $keys = [
            'entry_type',
            'allow_add',
            'allow_delete',
            'by_reference',
            'prototype',
            'prototype_data',
            'entry_options',
            'data'
        ];

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $options);
        }
    }

    public function testGetOptionsForApplicationEnvironment()
    {
        $applicationType = new ApplicationType();
        $applicationType->setType('foo');

        $application = new FooApplication();

        $applicationEnvironment = new ApplicationEnvironment();
        $applicationEnvironment->setApplication($application);

        $applicationTypeEnvironment = $this->setEntitytId(new ApplicationTypeEnvironment(), 1);
        $applicationTypeEnvironment->setApplicationType($applicationType);

        $settingDataValue = new SettingDataValue();
        $settingDataValue->setValue('[1,2,3]');

        $ateRepository = $this->getRepositoryMock();
        $sdvRepository = $this->getSettingDataValueRepositoryMock();
        $groovyScriptRepository = $this->getRepositoryMock();
        $atRepository = $this->getRepositoryMock();
        $jenkinsJobRepository = $this->getRepositoryMock();

        $atRepository
            ->expects($this->at(0))
            ->method('findOneBy')
            ->willReturn($applicationType);

        $ateRepository
            ->expects($this->at(0))
            ->method('findOneBy')
            ->willReturn($applicationTypeEnvironment);

        $sdvRepository
            ->expects($this->at(0))
            ->method('findOneByKey')
            ->willReturn($settingDataValue);

        for ($i = 1; $i <= 3; $i++) {
            $jenkinsJobRepository
                ->expects($this->at($i - 1))
                ->method('find')
                ->willReturn($this->setEntitytId(new JenkinsJob(), $i));
        }

        $repositories = [
            [
                'class' => ApplicationTypeEnvironment::class,
                'repository' => $ateRepository
            ],
            [
                'class' => SettingDataValue::class,
                'repository' => $sdvRepository
            ],
            [
                'class' => JenkinsGroovyScript::class,
                'repository' => $groovyScriptRepository
            ],
            [
                'class' => ApplicationType::class,
                'repository' => $atRepository
            ],
            [
                'class' => JenkinsJob::class,
                'repository' => $jenkinsJobRepository
            ],
            [
                'class' => JenkinsJob::class,
                'repository' => $jenkinsJobRepository
            ],
        ];

        $entityManager = $this->getEntityManagerMock($repositories);

        $fieldType = new JenkinsJobChoiceFieldType($entityManager);
        $fieldType->setOriginEntity($applicationEnvironment);

        $options = $fieldType->getOptions('');
        $keys = [
            'entry_type',
            'allow_add',
            'allow_delete',
            'by_reference',
            'prototype',
            'prototype_data',
            'entry_options',
            'data'
        ];

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $options);
        }
    }

    private function getSettingDataValueRepositoryMock()
    {
        $mock = $this
            ->getMockBuilder(SettingDataValueRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function getEntityManagerMock(array $repositories = array())
    {
        $mock = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $index = 0;

        foreach ($repositories as $repositoryArr) {
            $mock
                ->expects($this->at($index))
                ->method('getRepository')
                ->with($this->equalTo($repositoryArr['class']))
                ->willReturn($repositoryArr['repository']);

            $index++;
        }

        return $mock;
    }

    private function getRepositoryMock()
    {
        $mock = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function setEntitytId($entity, $id)
    {
        $reflectionObject = new \ReflectionObject($entity);
        $property = $reflectionObject->getProperty('id');
        $property->setAccessible(true);
        $property->setValue(
            $entity,
            $id
        );

        return $entity;
    }
}