<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Netgen\Layouts\API\Values\Config\ConfigAwareStruct;
use Netgen\Layouts\API\Values\Config\ConfigAwareStructTrait;
use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\API\Values\ParameterStructTrait;
use Netgen\Layouts\Block\BlockDefinitionInterface;

final class BlockUpdateStruct implements ParameterStruct, ConfigAwareStruct
{
    use ConfigAwareStructTrait;
    use ParameterStructTrait;

    /**
     * The locale which will be updated.
     *
     * Required.
     */
    public string $locale;

    /**
     * New view type of the block.
     */
    public ?string $viewType = null;

    /**
     * New item view type of the block.
     */
    public ?string $itemViewType = null;

    /**
     * New human readable name of the block.
     */
    public ?string $name = null;

    /**
     * New state of the always available flag.
     */
    public ?bool $alwaysAvailable = null;

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
     *
     * @param array<string, mixed> $values
     */
    public function fillParametersFromHash(BlockDefinitionInterface $blockDefinition, array $values, bool $doImport = false): void
    {
        $this->fillFromHash($blockDefinition, $values, $doImport);
    }
}
