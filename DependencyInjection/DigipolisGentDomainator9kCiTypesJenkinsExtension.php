<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\DependencyInjection;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\JenkinsCliService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

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
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $def = $container->getDefinition(JenkinsCliService::class);
        $def->addMethodCall('setJavaPath', [$config['java_path']]);
        $def->addMethodCall('setUrl', [$config['url']]);
        $def->addMethodCall('setCliPath', [$config['cli_path']]);
        $def->addMethodCall('setPrivateKeyPath', [$config['private_key_path']]);
    }
}
