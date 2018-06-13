<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Block\BlockType\BlockType;

final class CreateStructBuilder
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Creates a new block create struct.
     *
     * @param \Netgen\BlockManager\Block\BlockType\BlockType $blockType
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockCreateStruct
     */
    public function buildCreateStruct(BlockType $blockType)
    {
        $blockDefinition = $blockType->getDefinition();

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->name = $blockType->getDefaultName();
        $blockCreateStruct->fillParametersFromHash($blockDefinition, $blockType->getDefaultParameters());

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
                new CollectionCreateStruct()
            );
        }

        return $blockCreateStruct;
    }
}
