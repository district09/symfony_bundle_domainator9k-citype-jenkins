<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Tests\DependencyInjection;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\DependencyInjection\DigipolisGentDomainator9kCiTypesJenkinsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DigipolisGentDomainator9kSockExtensionTest extends TestCase
{

    public function testLoad()
    {
        $container = $this->getContainerBuilderMock();
        $container
            ->expects($this->atLeastOnce())
            ->method('fileExists');


        $configs = [];

        $extension = new DigipolisGentDomainator9kCiTypesJenkinsExtension();
        $extension->load($configs, $container);
    }

    private function getContainerBuilderMock()
    {
        $mock = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }
}
