<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils;

use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\BlockCreateStruct;
use Netgen\Layouts\API\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\Block\BlockType\BlockType;

use function count;

final class CreateStructBuilder
{
    public function __construct(
        private BlockService $blockService,
    ) {}

    /**
     * Creates a new block create struct.
     */
    public function buildCreateStruct(BlockType $blockType): BlockCreateStruct
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockType->definition);
        $blockCreateStruct->name = $blockType->defaultName;

        if (!isset($blockCreateStruct->viewType) || $blockType->defaultViewType !== '') {
            $blockCreateStruct->viewType = $blockType->defaultViewType;
        }

        if ($blockType->defaultItemViewType === '' && $blockType->definition->hasViewType($blockCreateStruct->viewType)) {
            $itemViewTypes = $blockType->definition
                ->getViewType($blockCreateStruct->viewType)
                ->itemViewTypeIdentifiers;

            if (count($itemViewTypes) > 0) {
                $blockCreateStruct->itemViewType = $itemViewTypes[0];
            }
        } elseif (!isset($blockCreateStruct->itemViewType) || $blockType->defaultItemViewType !== '') {
            $blockCreateStruct->itemViewType = $blockType->defaultItemViewType;
        }

        $blockCreateStruct->fillParametersFromHash($blockType->defaultParameters);

        foreach ($blockType->definition->collections as $collectionConfig) {
            $blockCreateStruct->addCollectionCreateStruct(
                $collectionConfig->identifier,
                new CollectionCreateStruct(),
            );
        }

        return $blockCreateStruct;
    }
}
