<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Config;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\Block\Registry\BlockTypeGroupRegistryInterface;
use Netgen\Layouts\Block\Registry\BlockTypeRegistryInterface;
use Netgen\Layouts\Serializer\Values\Value;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Serializer\Version;

final class LoadBlockTypes extends AbstractController
{
    /**
     * @var \Netgen\Layouts\Block\Registry\BlockTypeRegistryInterface
     */
    private $blockTypeRegistry;

    /**
     * @var \Netgen\Layouts\Block\Registry\BlockTypeGroupRegistryInterface
     */
    private $blockTypeGroupRegistry;

    public function __construct(
        BlockTypeRegistryInterface $blockTypeRegistry,
        BlockTypeGroupRegistryInterface $blockTypeGroupRegistry
    ) {
        $this->blockTypeRegistry = $blockTypeRegistry;
        $this->blockTypeGroupRegistry = $blockTypeGroupRegistry;
    }

    /**
     * Serializes the block types.
     */
    public function __invoke(): Value
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $blockTypeGroups = [];
        foreach ($this->blockTypeGroupRegistry->getBlockTypeGroups(true) as $blockTypeGroup) {
            if (count($blockTypeGroup->getBlockTypes(true)) > 0) {
                $blockTypeGroups[] = new VersionedValue($blockTypeGroup, Version::API_V1);
            }
        }

        $blockTypes = [];
        foreach ($this->blockTypeRegistry->getBlockTypes(true) as $blockType) {
            $blockTypes[] = new VersionedValue($blockType, Version::API_V1);
        }

        return new Value(
            [
                'block_type_groups' => $blockTypeGroups,
                'block_types' => $blockTypes,
            ]
        );
    }
}
