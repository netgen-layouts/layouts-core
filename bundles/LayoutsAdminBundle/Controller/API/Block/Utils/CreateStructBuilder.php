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

        if (!isset($blockCreateStruct->viewType) || $blockType->getDefaultViewType() !== '') {
            $blockCreateStruct->viewType = $blockType->getDefaultViewType();
        }

        if (!isset($blockCreateStruct->itemViewType) || $blockType->getDefaultItemViewType() !== '') {
            $blockCreateStruct->itemViewType = $blockType->getDefaultItemViewType();
        }

        $blockCreateStruct->fillParametersFromHash($blockType->getDefaultParameters());

        foreach ($blockDefinition->getCollections() as $collectionConfig) {
            $blockCreateStruct->addCollectionCreateStruct(
                $collectionConfig->getIdentifier(),
                new CollectionCreateStruct(),
            );
        }

        return $blockCreateStruct;
    }
}
