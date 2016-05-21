<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Configuration\Registry\BlockTypeRegistryInterface;
use Netgen\BlockManager\Configuration\Registry\SourceRegistryInterface;
use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

class ConfigController extends Controller
{
    /**
     * @var \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistryInterface
     */
    protected $blockTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\SourceRegistryInterface
     */
    protected $sourceRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistryInterface $blockTypeRegistry
     * @param \Netgen\BlockManager\Configuration\Registry\SourceRegistryInterface $sourceRegistry
     */
    public function __construct(BlockTypeRegistryInterface $blockTypeRegistry, SourceRegistryInterface $sourceRegistry)
    {
        $this->blockTypeRegistry = $blockTypeRegistry;
        $this->sourceRegistry = $sourceRegistry;
    }

    /**
     * Serializes the block types.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function getBlockTypes()
    {
        $blockTypeGroups = array_map(
            function (BlockTypeGroup $blockTypeGroup) {
                return new VersionedValue($blockTypeGroup, Version::API_V1);
            },
            array_values($this->blockTypeRegistry->allBlockTypeGroups())
        );

        $blockTypes = array_map(
            function (BlockType $blockType) {
                return new VersionedValue($blockType, Version::API_V1);
            },
            array_values($this->blockTypeRegistry->allBlockTypes())
        );

        return new ValueArray(
            array(
                'block_type_groups' => $blockTypeGroups,
                'block_types' => $blockTypes,
            )
        );
    }

    /**
     * Serializes the collection sources.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function getSources()
    {
        $sources = array_map(
            function (Source $source) {
                return new VersionedValue($source, Version::API_V1);
            },
            array_values($this->sourceRegistry->all())
        );

        return new ValueArray($sources);
    }
}
