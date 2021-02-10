<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\StructBuilder;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockCreateStruct;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Block\BlockDefinitionInterface;

final class BlockStructBuilder
{
    private ConfigStructBuilder $configStructBuilder;

    public function __construct(ConfigStructBuilder $configStructBuilder)
    {
        $this->configStructBuilder = $configStructBuilder;
    }

    /**
     * Creates a new block create struct from data found in provided block definition.
     */
    public function newBlockCreateStruct(BlockDefinitionInterface $blockDefinition): BlockCreateStruct
    {
        $viewTypeIdentifier = $blockDefinition->getViewTypeIdentifiers()[0];
        $viewType = $blockDefinition->getViewType($viewTypeIdentifier);
        $itemViewTypeIdentifier = $viewType->getItemViewTypeIdentifiers()[0];

        $blockCreateStruct = new BlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = $viewTypeIdentifier;
        $blockCreateStruct->itemViewType = $itemViewTypeIdentifier;
        $blockCreateStruct->isTranslatable = $blockDefinition->isTranslatable();
        $blockCreateStruct->alwaysAvailable = true;

        return $blockCreateStruct;
    }

    /**
     * Creates a new block update struct in specified locale.
     *
     * If block is provided, initial data is copied from the block.
     */
    public function newBlockUpdateStruct(string $locale, ?Block $block = null): BlockUpdateStruct
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->locale = $locale;

        if (!$block instanceof Block) {
            return $blockUpdateStruct;
        }

        $blockUpdateStruct->viewType = $block->getViewType();
        $blockUpdateStruct->itemViewType = $block->getItemViewType();
        $blockUpdateStruct->name = $block->getName();
        $blockUpdateStruct->alwaysAvailable = $block->isAlwaysAvailable();
        $blockUpdateStruct->fillParametersFromBlock($block);

        $this->configStructBuilder->buildConfigUpdateStructs($block, $blockUpdateStruct);

        return $blockUpdateStruct;
    }
}
