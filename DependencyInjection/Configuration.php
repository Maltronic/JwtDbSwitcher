<?php

namespace Maltronic\Bundle\JwtDbSwitcher\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('maltronic_jwt_db_switcher');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('databases')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end();

        return $treeBuilder;
    }
}
