<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

$treeBuilder = new TreeBuilder('app');

$treeBuilder->getRootNode()
    ->children()
        ->arrayNode('element')->isRequired()
            ->children()
                ->scalarNode('child_element')->isRequired()->defaultValue('value')->end()
            ->end()
        ->end()
    ->end()
;

return $treeBuilder->buildTree();
