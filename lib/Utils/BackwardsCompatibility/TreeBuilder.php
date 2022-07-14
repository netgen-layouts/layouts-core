<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils\BackwardsCompatibility;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder as BaseTreeBuilder;

use function is_callable;

/**
 * BC layer for Symfony 4.2 which deprecated building TreeBuilder objects without root node
 * Remove when support for Symfony 3.4 and lower ends.
 */
final class TreeBuilder extends BaseTreeBuilder
{
    public function __construct(string $name, string $type = 'array', ?NodeBuilder $builder = null)
    {
        if (is_callable([BaseTreeBuilder::class, '__construct'])) {
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
