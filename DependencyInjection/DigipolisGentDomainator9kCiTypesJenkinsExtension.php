<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class DigipolisGentDomainator9kCiTypesJenkinsExtension
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\DependencyInjection
 */
class DigipolisGentDomainator9kCiTypesJenkinsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
