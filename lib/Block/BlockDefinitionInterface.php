<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;
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
    public function getIdentifier();

    /**
     * Returns the block definition human readable name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the block definition icon.
     *
     * @return string
     */
    public function getIcon();

    /**
     * Returns if the block will be translatable when created.
     *
     * @return bool
     */
    public function isTranslatable();

    /**
     * Returns all collections.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection[]
     */
    public function getCollections();

    /**
     * Returns if the block definition has a collection with provided identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasCollection($identifier);

    /**
     * Returns the collection for provided collection identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If collection does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection
     */
    public function getCollection($identifier);

    /**
     * Returns all forms.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form[]
     */
    public function getForms();

    /**
     * Returns if the block definition has a form with provided name.
     *
     * @param string $formName
     *
     * @return bool
     */
    public function hasForm($formName);

    /**
     * Returns the form for provided form name.
     *
     * @param string $formName
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If form does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form
     */
    public function getForm($formName);

    /**
     * Returns the block definition view types.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType[]
     */
    public function getViewTypes();

    /**
     * Returns the block definition view type identifiers.
     *
     * @return string[]
     */
    public function getViewTypeIdentifiers();

    /**
     * Returns if the block definition has a view type with provided identifier.
     *
     * @param string $viewType
     *
     * @return bool
     */
    public function hasViewType($viewType);

    /**
     * Returns the view type with provided identifier.
     *
     * @param string $viewType
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If view type does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType
     */
    public function getViewType($viewType);

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Block\DynamicParameters
     */
    public function getDynamicParameters(Block $block);

    /**
     * Returns if the provided block is dependent on a context, i.e. currently displayed page.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function isContextual(Block $block);

    /**
     * Returns if the block definition has a plugin with provided FQCN.
     *
     * @param string $className
     *
     * @return bool
     */
    public function hasPlugin($className);

    /**
     * Returns if the provided block is cacheable.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function isCacheable(Block $block);
}
