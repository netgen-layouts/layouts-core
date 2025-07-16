<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils\BackwardsCompatibility;

use ReflectionClass;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder as BaseTreeBuilder;

/**
 * BC layer for Symfony 4.2 which deprecated building TreeBuilder objects without root node
 * Remove when support for Symfony 3.4 and lower ends.
 */
final class TreeBuilder extends BaseTreeBuilder
{
    public function __construct(string $name, string $type = 'array', ?NodeBuilder $builder = null)
    {
        $treeBuilderReflection = new ReflectionClass(BaseTreeBuilder::class);

        if ($treeBuilderReflection->hasMethod('__construct')) {
            parent::__construct($name, $type, $builder);

            return;
        }

        $this->root = $this->root($name, $type, $builder);
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition|\Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    public function getRootNode(): NodeDefinition
    {
        return $this->root;
    }
}
