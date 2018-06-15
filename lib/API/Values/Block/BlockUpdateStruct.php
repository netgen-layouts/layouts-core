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
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     */
    public function fillParameters(BlockDefinitionInterface $blockDefinition, array $values = []): void
    {
        $this->fill($blockDefinition, $values);
    }

    /**
     * Fills the parameter values based on provided block.
     */
    public function fillParametersFromBlock(Block $block): void
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
     */
    public function fillParametersFromHash(BlockDefinitionInterface $blockDefinition, array $values = [], bool $doImport = false): void
    {
        $this->fillFromHash($blockDefinition, $values, $doImport);
    }
}
