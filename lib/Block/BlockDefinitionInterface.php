<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Collection;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Form;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface;
use Netgen\Layouts\Config\ConfigDefinitionAwareInterface;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;

/**
 * Block definition represents the model of the block, built from configuration.
 * This model specifies which parameters, view types and item view types
 * the block can have.
 */
interface BlockDefinitionInterface extends ParameterDefinitionCollectionInterface, ConfigDefinitionAwareInterface
{
    /**
     * Returns the block definition identifier.
     */
    public string $identifier { get; }

    /**
     * Returns the block definition human readable name.
     */
    public string $name { get; }

    /**
     * Returns the block definition icon.
     */
    public ?string $icon { get; }

    /**
     * Returns the handler object of this block definition.
     */
    public BlockDefinitionHandlerInterface $handler { get; }

    /**
     * Returns all block definition handler plugins.
     *
     * @var \Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface[]
     */
    public array $handlerPlugins { get; }

    /**
     * Returns if the block will be translatable when created.
     */
    public bool $isTranslatable { get; }

    /**
     * Returns all collections.
     *
     * @var \Netgen\Layouts\Block\BlockDefinition\Configuration\Collection[]
     */
    public array $collections { get; }

    /**
     * Returns all forms.
     *
     * @var \Netgen\Layouts\Block\BlockDefinition\Configuration\Form[]
     */
    public array $forms { get; }

    /**
     * Returns the block definition view types.
     *
     * @var \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType[]
     */
    public array $viewTypes { get; }

    /**
     * Returns the block definition view type identifiers.
     *
     * @var string[]
     */
    public array $viewTypeIdentifiers { get; }

    /**
     * Returns if the block definition has a collection with provided identifier.
     */
    public function hasCollection(string $identifier): bool;

    /**
     * Returns the collection for provided collection identifier.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockDefinitionException If collection does not exist
     */
    public function getCollection(string $identifier): Collection;

    /**
     * Returns if the block definition has a form with provided name.
     */
    public function hasForm(string $formName): bool;

    /**
     * Returns the form for provided form name.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockDefinitionException If form does not exist
     */
    public function getForm(string $formName): Form;

    /**
     * Returns the block definition view types.
     *
     * @return \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType[]
     */
    public function getBlockViewTypes(Block $block): array;

    /**
     * Returns the block definition view type identifiers.
     *
     * @return string[]
     */
    public function getBlockViewTypeIdentifiers(Block $block): array;

    /**
     * Returns if the block definition has a view type with provided identifier.
     */
    public function hasViewType(string $viewType, ?Block $block = null): bool;

    /**
     * Returns the view type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockDefinitionException If view type does not exist
     */
    public function getViewType(string $viewType, ?Block $block = null): ViewType;

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     */
    public function getDynamicParameters(Block $block): DynamicParameters;

    /**
     * Returns if the provided block is dependent on a context, i.e. currently displayed page.
     */
    public function isContextual(Block $block): bool;

    /**
     * Returns if the block definition handler has a plugin with provided FQCN.
     *
     * @param class-string<\Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface> $className
     */
    public function hasHandlerPlugin(string $className): bool;

    /**
     * Returns the block definition handler plugin with provided FQCN.
     *
     * @param class-string<\Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface> $className
     */
    public function getHandlerPlugin(string $className): PluginInterface;
}
