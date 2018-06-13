<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Value;

final class BlockCreateStruct extends Value implements ParameterStruct, ConfigAwareStruct
{
    use ParameterStructTrait;
    use ConfigAwareStructTrait;

    /**
     * Block definition to create the new block from.
     *
     * Required.
     *
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public $definition;

    /**
     * View type of the new block.
     *
     * Required.
     *
     * @var string
     */
    public $viewType;

    /**
     * Item view type of the new block.
     *
     * Required.
     *
     * @var string
     */
    public $itemViewType;

    /**
     * Human readable name of the block.
     *
     * @var string|null
     */
    public $name;

    /**
     * Specifies if the block will be translatable.
     *
     * Required.
     *
     * @var bool
     */
    public $isTranslatable;

    /**
     * Specifies if the block will be always available.
     *
     * Required.
     *
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * The list of collections to create in the block.
     *
     * The keys are collection identifiers, while the values are instances of CollectionCreateStruct objects.
     *
     * @var \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct[]
     */
    protected $collectionCreateStructs = [];

    /**
     * Adds a collection create struct with specified identifier to the struct.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct $collectionCreateStruct
     */
    public function addCollectionCreateStruct($identifier, CollectionCreateStruct $collectionCreateStruct)
    {
        $this->collectionCreateStructs[$identifier] = $collectionCreateStruct;
    }

    /**
     * Returns all collection create structs from this struct.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct[]
     */
    public function getCollectionCreateStructs()
    {
        return $this->collectionCreateStructs;
    }

    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     * @param array $values
     */
    public function fillParameters(BlockDefinitionInterface $blockDefinition, array $values = [])
    {
        $this->fill($blockDefinition, $values);
    }

    /**
     * Fills the parameter values based on provided block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     */
    public function fillParametersFromBlock(Block $block)
    {
        $this->fillFromValue($block->getDefinition(), $block);
    }

    /**
     * Fills the parameter values based on provided array of values.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     *
     * If $doImport is set to true, the values will be considered as coming from an import,
     * meaning it will be processed using ParameterTypeInterface::import method instead of
     * ParameterTypeInterface::fromHash method.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     * @param array $values
     * @param bool $doImport
     */
    public function fillParametersFromHash(BlockDefinitionInterface $blockDefinition, array $values = [], $doImport = false)
    {
        $this->fillFromHash($blockDefinition, $values, $doImport);
    }
}
