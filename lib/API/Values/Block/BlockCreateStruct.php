<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Netgen\Layouts\API\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\API\Values\Config\ConfigAwareStruct;
use Netgen\Layouts\API\Values\Config\ConfigAwareStructTrait;
use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\API\Values\ParameterStructTrait;
use Netgen\Layouts\Block\BlockDefinitionInterface;

final class BlockCreateStruct implements ParameterStruct, ConfigAwareStruct
{
    use ConfigAwareStructTrait;
    use ParameterStructTrait;

    /**
     * View type of the new block.
     *
     * Required.
     */
    public string $viewType;

    /**
     * Item view type of the new block.
     *
     * Required.
     */
    public string $itemViewType;

    /**
     * Human readable name of the block.
     */
    public ?string $name = '';

    /**
     * Specifies if the block will be translatable.
     *
     * Required.
     */
    public bool $isTranslatable;

    /**
     * Specifies if the block will be always available.
     *
     * Required.
     */
    public bool $alwaysAvailable;

    /**
     * Block definition to create the new block from.
     */
    private BlockDefinitionInterface $definition;

    /**
     * The list of collections to create in the block.
     *
     * The keys are collection identifiers, while the values are instances of CollectionCreateStruct objects.
     *
     * @var \Netgen\Layouts\API\Values\Collection\CollectionCreateStruct[]
     */
    private array $collectionCreateStructs = [];

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
     * @return \Netgen\Layouts\API\Values\Collection\CollectionCreateStruct[]
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
     *
     * @param array<string, mixed> $values
     */
    public function fillParametersFromHash(array $values, bool $doImport = false): void
    {
        $this->fillFromHash($this->definition, $values, $doImport);
    }
}
