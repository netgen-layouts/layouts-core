<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Block\BlockDefinitionInterface;

final class BlockCreateStruct implements ParameterStruct, ConfigAwareStruct
{
    use ParameterStructTrait;
    use ConfigAwareStructTrait;

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
     * Block definition to create the new block from.
     *
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private $definition;

    /**
     * The list of collections to create in the block.
     *
     * The keys are collection identifiers, while the values are instances of CollectionCreateStruct objects.
     *
     * @var \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct[]
     */
    private $collectionCreateStructs = [];

    public function __construct(BlockDefinitionInterface $definition)
    {
        $this->definition = $definition;
        $this->fillDefault($this->definition);
    }

    /**
     * Returns the block definition that will be used to create a block with this struct.
     */
    public function getDefinition(): BlockDefinitionInterface
    {
        return $this->definition;
    }

    /**
     * Adds a collection create struct with specified identifier to the struct.
     */
    public function addCollectionCreateStruct(string $identifier, CollectionCreateStruct $collectionCreateStruct): void
    {
        $this->collectionCreateStructs[$identifier] = $collectionCreateStruct;
    }

    /**
     * Returns all collection create structs from this struct.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct[]
     */
    public function getCollectionCreateStructs(): array
    {
        return $this->collectionCreateStructs;
    }

    /**
     * Fills the parameter values based on provided array of values.
     *
     * If any of the parameters is missing from the input array, the default value
     * based on parameter definition from the block definition will be used.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     *
     * If $doImport is set to true, the values will be considered as coming from an import,
     * meaning it will be processed using ParameterTypeInterface::import method instead of
     * ParameterTypeInterface::fromHash method.
     */
    public function fillParametersFromHash(array $values, bool $doImport = false): void
    {
        $this->fillFromHash($this->definition, $values, $doImport);
    }
}
