<?php

namespace Vinorcola\ImportBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Vinorcola\ImportBundle\Model\ImportConsumerInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vinorcola_import');

        $rootNode
            ->fixXmlConfig('import')
            ->children()
                ->scalarNode('temporaryDirectory')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end() // temporaryDirectory
                ->arrayNode('imports')
                    ->requiresAtLeastOneElement()
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('route_prefix')
                                ->isRequired()
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('url')->end()
                                ->end()
                            ->end() // route_prefix
                            ->arrayNode('mapping')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->scalarPrototype()->end()
                            ->end() // mapping
                            ->scalarNode('service')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->validate()
                                    ->ifTrue(function ($value) {
                                        return !is_string($value) || !is_subclass_of($value, ImportConsumerInterface::class);
                                    })
                                    ->thenInvalid('Class must implement "' . ImportConsumerInterface::class . '".')
                                ->end()
                            ->end() // service
                        ->end()
                    ->end()
                ->end() // imports
            ->end()
        ;

        return $treeBuilder;
    }
}
