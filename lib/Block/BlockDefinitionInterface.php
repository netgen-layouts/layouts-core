<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Config\ConfigDefinitionAwareInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;

/**
 * Block definition represents the model of the block, built from configuration.
 * This model specifies which parameters, view types and item view types
 * the block can have.
 */
interface BlockDefinitionInterface extends ParameterDefinitionCollectionInterface, ConfigDefinitionAwareInterface
{
    /**
     * Returns the block definition identifier.
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Returns the block definition human readable name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the block definition icon.
     */
    public function getIcon(): ?string;

    /**
     * Returns if the block will be translatable when created.
     *
     * @return bool
     */
    public function isTranslatable(): bool;

    /**
     * Returns all collections.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection[]
     */
    public function getCollections(): array;

    /**
     * Returns if the block definition has a collection with provided identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasCollection(string $identifier): bool;

    /**
     * Returns the collection for provided collection identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If collection does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection
     */
    public function getCollection(string $identifier): Collection;

    /**
     * Returns all forms.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form[]
     */
    public function getForms(): array;

    /**
     * Returns if the block definition has a form with provided name.
     *
     * @param string $formName
     *
     * @return bool
     */
    public function hasForm(string $formName): bool;

    /**
     * Returns the form for provided form name.
     *
     * @param string $formName
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If form does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form
     */
    public function getForm(string $formName): Form;

    /**
     * Returns the block definition view types.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType[]
     */
    public function getViewTypes(): array;

    /**
     * Returns the block definition view type identifiers.
     *
     * @return string[]
     */
    public function getViewTypeIdentifiers(): array;

    /**
     * Returns if the block definition has a view type with provided identifier.
     *
     * @param string $viewType
     *
     * @return bool
     */
    public function hasViewType(string $viewType): bool;

    /**
     * Returns the view type with provided identifier.
     *
     * @param string $viewType
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If view type does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType
     */
    public function getViewType(string $viewType): ViewType;

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Block\DynamicParameters
     */
    public function getDynamicParameters(Block $block): DynamicParameters;

    /**
     * Returns if the provided block is dependent on a context, i.e. currently displayed page.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function isContextual(Block $block): bool;

    /**
     * Returns if the block definition has a plugin with provided FQCN.
     *
     * @param string $className
     *
     * @return bool
     */
    public function hasPlugin(string $className): bool;

    /**
     * Returns if the provided block is cacheable.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function isCacheable(Block $block): bool;
}
