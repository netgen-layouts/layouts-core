<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Value;

final class BlockUpdateStruct extends Value implements ParameterStruct, ConfigAwareStruct
{
    use ParameterStructTrait;
    use ConfigAwareStructTrait;

    /**
     * The locale which will be updated.
     *
     * Required.
     *
     * @var string
     */
    public $locale;

    /**
     * New view type of the block.
     *
     * @var string|null
     */
    public $viewType;

    /**
     * New item view type of the block.
     *
     * @var string|null
     */
    public $itemViewType;

    /**
     * New human readable name of the block.
     *
     * @var string|null
     */
    public $name;

    /**
     * New state of the always available flag.
     *
     * @var bool|null
     */
    public $alwaysAvailable;

    /**
     * Fills the struct with the default parameter values as defined in provided
     * block definition.
     */
    public function fillDefaultParameters(BlockDefinitionInterface $blockDefinition): void
    {
        $this->fillDefault($blockDefinition);
    }

    /**
     * Fills the parameter values based on provided block.
     */
    public function fillParametersFromBlock(Block $block): void
    {
        $this->fillFromCollection($block->getDefinition(), $block);
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
    public function fillParametersFromHash(BlockDefinitionInterface $blockDefinition, array $values = [], bool $doImport = false): void
    {
        $this->fillFromHash($blockDefinition, $values, $doImport);
    }
}
