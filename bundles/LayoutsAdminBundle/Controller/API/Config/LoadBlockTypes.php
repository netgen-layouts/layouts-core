<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\ArrayValue;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\Block\Registry\BlockTypeGroupRegistry;
use Netgen\Layouts\Block\Registry\BlockTypeRegistry;

use function count;

final class LoadBlockTypes extends AbstractController
{
    private BlockTypeRegistry $blockTypeRegistry;

    private BlockTypeGroupRegistry $blockTypeGroupRegistry;

    public function __construct(
        BlockTypeRegistry $blockTypeRegistry,
        BlockTypeGroupRegistry $blockTypeGroupRegistry
    ) {
        $this->blockTypeRegistry = $blockTypeRegistry;
        $this->blockTypeGroupRegistry = $blockTypeGroupRegistry;
    }

    /**
     * Serializes the block types.
     */
    public function __invoke(): ArrayValue
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $blockTypeGroups = [];
        foreach ($this->blockTypeGroupRegistry->getBlockTypeGroups(true) as $blockTypeGroup) {
            if (count($blockTypeGroup->getBlockTypes(true)) > 0) {
                $blockTypeGroups[] = new Value($blockTypeGroup);
            }
        }

        $blockTypes = [];
        foreach ($this->blockTypeRegistry->getBlockTypes(true) as $blockType) {
            $blockTypes[] = new Value($blockType);
        }

        return new ArrayValue(
            [
                'block_type_groups' => $blockTypeGroups,
                'block_types' => $blockTypes,
            ],
        );
    }
}
