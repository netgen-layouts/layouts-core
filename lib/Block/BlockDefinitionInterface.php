<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Collection;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Form;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
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
    public function getIdentifier(): string;

    /**
     * Returns the block definition human readable name.
     */
    public function getName(): string;

    /**
     * Returns the block definition icon.
     */
    public function getIcon(): ?string;

    /**
     * Returns if the block will be translatable when created.
     */
    public function isTranslatable(): bool;

    /**
     * Returns all collections.
     *
     * @return \Netgen\Layouts\Block\BlockDefinition\Configuration\Collection[]
     */
    public function getCollections(): array;

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
     * Returns all forms.
     *
     * @return \Netgen\Layouts\Block\BlockDefinition\Configuration\Form[]
     */
    public function getForms(): array;

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
    public function getViewTypes(): array;

    /**
     * Returns the block definition view type identifiers.
     *
     * @return string[]
     */
    public function getViewTypeIdentifiers(): array;

    /**
     * Returns if the block definition has a view type with provided identifier.
     */
    public function hasViewType(string $viewType): bool;

    /**
     * Returns the view type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockDefinitionException If view type does not exist
     */
    public function getViewType(string $viewType): ViewType;

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     */
    public function getDynamicParameters(Block $block): DynamicParameters;

    /**
     * Returns if the provided block is dependent on a context, i.e. currently displayed page.
     */
    public function isContextual(Block $block): bool;

    /**
     * Returns if the block definition has a plugin with provided FQCN.
     *
     * @param class-string $className
     */
    public function hasPlugin(string $className): bool;
}
