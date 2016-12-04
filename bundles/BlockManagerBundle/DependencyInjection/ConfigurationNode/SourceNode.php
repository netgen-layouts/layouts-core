<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class SourceNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for sources.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sources');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('source')
            ->prototype('array')
                ->canBeDisabled()
                ->beforeNormalization()
                    ->ifTrue(function ($v) {
                        return is_array($v) && !array_key_exists('queries', $v);
                    })
                    ->then(function ($v) {
                        // Key that should not be rewritten to the query config
                        $excludedKeys = array('name' => true);
                        $query = array();
                        foreach ($v as $key => $value) {
                            if (isset($excludedKeys[$key])) {
                                continue;
                            }
                            $query[$key] = $v[$key];
                            unset($v[$key]);
                        }
                        $v['queries'] = array('default' => $query);

                        return $v;
                    })
                ->end()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('queries')
                        ->isRequired()
                        ->performNoDeepMerging()
                        ->requiresAtLeastOneElement()
                        ->prototype('array')
                            ->children()
                                ->scalarNode('query_type')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('default_parameters')
                                    ->defaultValue(array())
                                    ->performNoDeepMerging()
                                    ->requiresAtLeastOneElement()
                                    ->useAttributeAsKey('parameter')
                                    ->prototype('variable')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
