<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils;

use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\BlockCreateStruct;
use Netgen\Layouts\API\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\Block\BlockType\BlockType;

final class CreateStructBuilder
{
    private BlockService $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Creates a new block create struct.
     */
    public function buildCreateStruct(BlockType $blockType): BlockCreateStruct
    {
        $blockDefinition = $blockType->getDefinition();

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->name = $blockType->getDefaultName();
        $blockCreateStruct->fillParametersFromHash($blockType->getDefaultParameters());

        if ($blockDefinition->hasViewType($blockType->getDefaultViewType())) {
            $viewType = $blockDefinition->getViewType($blockType->getDefaultViewType());

            $blockCreateStruct->viewType = $blockType->getDefaultViewType();
            $blockCreateStruct->itemViewType = $viewType->hasItemViewType($blockType->getDefaultItemViewType()) ?
                $blockType->getDefaultItemViewType() :
                $viewType->getItemViewTypeIdentifiers()[0];
        }

        foreach ($blockDefinition->getCollections() as $collectionConfig) {
            $blockCreateStruct->addCollectionCreateStruct(
                $collectionConfig->getIdentifier(),
                new CollectionCreateStruct(),
            );
        }

        return $blockCreateStruct;
    }
}
