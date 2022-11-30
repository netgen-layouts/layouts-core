<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Layouts\Block\Form\ContentEditType;
use Netgen\Layouts\Block\Form\DesignEditType;
use Netgen\Layouts\Block\Form\FullEditType;
use Netgen\Layouts\Utils\BackwardsCompatibility\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use function array_unique;
use function array_values;
use function is_array;
use function is_string;

final class BlockDefinitionNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('block_definitions');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('block_definition')
            ->arrayPrototype()
                ->canBeDisabled()
                ->children()
                    ->scalarNode('handler')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('config_provider')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('icon')
                        ->defaultValue(null)
                        ->validate()
                            ->ifTrue(
                                static fn ($v): bool => !($v === null || (is_string($v) && $v !== '')),
                            )
                            ->thenInvalid('Icon path needs to be a non empty string or null.')
                        ->end()
                    ->end()
                    ->booleanNode('translatable')
                        ->defaultFalse()
                    ->end()
                    ->arrayNode('collections')
                        ->children()
                            ->arrayNode('default')
                                ->addDefaultsIfNotSet()
                                ->validate()
                                    ->ifTrue(
                                        static function (array $v): bool {
                                            if (!isset($v['valid_item_types'], $v['valid_query_types'])) {
                                                return false;
                                            }

                                            return $v['valid_item_types'] === [] && $v['valid_query_types'] === [];
                                        },
                                    )
                                    ->thenInvalid('Collections need to allow at least one item type or at least one query type.')
                                ->end()
                                ->children()
                                    ->variableNode('valid_item_types')
                                        ->defaultNull()
                                        ->validate()
                                            ->ifTrue(
                                                static fn ($v): bool => $v !== null && !is_array($v),
                                            )
                                            ->thenInvalid('The value should be null or an array')
                                        ->end()
                                        ->validate()
                                            ->always(
                                                static fn ($v) => is_array($v) ? array_values(array_unique($v)) : $v,
                                            )
                                        ->end()
                                    ->end()
                                    ->variableNode('valid_query_types')
                                        ->defaultNull()
                                        ->validate()
                                            ->ifTrue(
                                                static fn ($v): bool => $v !== null && !is_array($v),
                                            )
                                            ->thenInvalid('The value should be null or an array')
                                        ->end()
                                        ->validate()
                                            ->always(
                                                static fn ($v) => is_array($v) ? array_values(array_unique($v)) : $v,
                                            )
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('forms')
                        ->addDefaultsIfNotSet()
                        ->validate()
                            ->always(
                                static function (array $v): array {
                                    $exception = new InvalidConfigurationException('Block definition must either have a full form or content and design forms.');

                                    if ($v['full']['enabled'] && ($v['design']['enabled'] || $v['content']['enabled'])) {
                                        throw $exception;
                                    }

                                    if (!$v['full']['enabled']) {
                                        if ($v['design']['enabled'] && !$v['content']['enabled']) {
                                            throw $exception;
                                        }

                                        if (!$v['design']['enabled'] && $v['content']['enabled']) {
                                            throw $exception;
                                        }
                                    }

                                    return $v;
                                },
                            )
                        ->end()
                        ->children()
                            ->arrayNode('full')
                                ->addDefaultsIfNotSet()
                                ->canBeDisabled()
                                ->children()
                                    ->scalarNode('type')
                                        ->treatNullLike(FullEditType::class)
                                        ->defaultValue(FullEditType::class)
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('design')
                                ->addDefaultsIfNotSet()
                                ->canBeEnabled()
                                ->children()
                                    ->scalarNode('type')
                                        ->treatNullLike(DesignEditType::class)
                                        ->defaultValue(DesignEditType::class)
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('content')
                                ->addDefaultsIfNotSet()
                                ->canBeEnabled()
                                ->children()
                                    ->scalarNode('type')
                                        ->treatNullLike(ContentEditType::class)
                                        ->defaultValue(ContentEditType::class)
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('view_types')
                        ->isRequired()
                        ->requiresAtLeastOneElement()
                        ->useAttributeAsKey('view_type')
                        ->arrayPrototype()
                            ->canBeDisabled()
                            ->validate()
                                ->ifTrue(
                                    static fn (array $v): bool => $v['enabled'] !== true,
                                )
                                ->then(
                                    static fn (array $v): array => [
                                        'name' => 'Disabled',
                                        'enabled' => false,
                                        'item_view_types' => [],
                                        'valid_parameters' => null,
                                    ],
                                )
                            ->end()
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('item_view_types')
                                    ->defaultValue(['standard' => ['name' => 'Standard', 'enabled' => true]])
                                    ->requiresAtLeastOneElement()
                                    ->useAttributeAsKey('item_view_type')
                                    ->arrayPrototype()
                                        ->canBeDisabled()
                                        ->validate()
                                            ->ifTrue(
                                                static fn (array $v): bool => $v['enabled'] === true && !isset($v['name']),
                                            )
                                            ->thenInvalid('Item view type name must be specified')
                                        ->end()
                                        ->validate()
                                            ->ifTrue(
                                                static fn (array $v): bool => $v['enabled'] !== true,
                                            )
                                            ->then(
                                                static fn (array $v): array => [
                                                    'name' => 'Disabled',
                                                    'enabled' => false,
                                                ],
                                            )
                                        ->end()
                                        ->children()
                                            ->scalarNode('name')
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->variableNode('valid_parameters')
                                    ->defaultNull()
                                    ->validate()
                                        ->ifTrue(
                                            static fn ($v): bool => $v !== null && !is_array($v),
                                        )
                                        ->thenInvalid('The value should be null or an array')
                                    ->end()
                                    ->validate()
                                        ->always(
                                            static fn ($v) => is_array($v) ? array_values(array_unique($v)) : $v,
                                        )
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('defaults')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('name')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('view_type')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('item_view_type')
                                ->defaultValue('')
                            ->end()
                            ->arrayNode('parameters')
                                ->defaultValue([])
                                ->performNoDeepMerging()
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('parameter')
                                ->variablePrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
