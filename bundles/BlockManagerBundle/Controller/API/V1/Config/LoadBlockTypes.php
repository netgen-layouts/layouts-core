<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config;

use Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistryInterface;
use Netgen\BlockManager\Block\Registry\BlockTypeRegistryInterface;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

final class LoadBlockTypes extends Controller
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeRegistryInterface
     */
    private $blockTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistryInterface
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
