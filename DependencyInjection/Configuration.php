<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('domainator_jenkins');
        $rootNode
            ->children()
            ->scalarNode('java_path')->cannotBeEmpty()->end()
            ->scalarNode('url')->cannotBeEmpty()->end()
            ->scalarNode('cli_path')->cannotBeEmpty()->end()
            ->scalarNode('private_key_path')->end()
            ->end();

        return $treeBuilder;
    }

}