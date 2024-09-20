<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\FieldType;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\FieldType\JenkinsServerChoiceFieldType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class JenkinsServerChoiceFieldTypeTest extends TestCase
{

    public function testGetName()
    {
        $this->assertEquals('jenkins_server_choice', JenkinsServerChoiceFieldType::getName());
    }

    public function testGetFormType()
    {
        $entityManager = $this->getEntityManagerMock();
        $fieldType = new JenkinsServerChoiceFieldType($entityManager);
        $this->assertEquals(ChoiceType::class, $fieldType->getFormType());
    }

    public function testEncodeValue()
    {
        $entityManager = $this->getEntityManagerMock();
        $fieldType = new JenkinsServerChoiceFieldType($entityManager);
        $result = $fieldType->encodeValue('foo');
        $this->assertEquals('foo', $result);
    }

    public function testDecodeValue()
    {
        $jenkinsServer = new JenkinsServer();

        $repository = $this->getRepositoryMock();

        $repository
            ->expects($this->atLeastOnce())
            ->method('find')
            ->willReturn($jenkinsServer);

        $entityManager = $this->getEntityManagerMock();

        $entityManager
            ->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with($this->equalTo(JenkinsServer::class))
            ->willReturn($repository);

        $fieldType = new JenkinsServerChoiceFieldType($entityManager);
        $result = $fieldType->decodeValue('1');
        $this->assertInstanceOf(JenkinsServer::class, $result);
    }

    public function testGetGetOptions()
    {

        $jenkinsServers = new ArrayCollection();

        $jenkinsServer = $this->setEntitytId(new JenkinsServer(),1);
        $jenkinsServer->setName('Jenkins server 1');
        $jenkinsServers->add($jenkinsServer);

        $jenkinsServer = $this->setEntitytId(new JenkinsServer(),2);
        $jenkinsServer->setName('Jenkins server 2');
        $jenkinsServers->add($jenkinsServer);

        $repository = $this->getRepositoryMock();

        $repository
            ->expects($this->atLeastOnce())
            ->method('findAll')
            ->willReturn($jenkinsServers);

        $entityManager = $this->getEntityManagerMock();

        $entityManager
            ->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with($this->equalTo(JenkinsServer::class))
            ->willReturn($repository);

        $fieldType = new JenkinsServerChoiceFieldType($entityManager);
        $options = $fieldType->getOptions('1');

        $this->assertArrayHasKey('choices',$options);
        $this->assertArrayHasKey('data',$options);
        $this->assertEquals(1,$options['data']);
    }

    private function getRepositoryMock()
    {
        $mock = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    private function getEntityManagerMock()
    {
        $mock = $this
            ->getMockBuilder(EntityManagerInterface::class)
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
