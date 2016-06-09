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
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

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
        $blockTypeGroups = array();
        foreach ($this->blockTypeRegistry->getBlockTypeGroups() as $blockTypeGroup) {
            if ($blockTypeGroup->isEnabled()) {
                $blockTypeGroups[] = new VersionedValue($blockTypeGroup, Version::API_V1);
            }
        }

        $blockTypes = array();
        foreach ($this->blockTypeRegistry->getBlockTypes() as $blockType) {
            if ($blockType->isEnabled()) {
                $blockTypes[] = new VersionedValue($blockType, Version::API_V1);
            }
        }

        return new ValueArray(
            array(
                'block_type_groups' => $blockTypeGroups,
                'block_types' => $blockTypes,
            )
        );
    }

    /**
     * Serializes the layout types.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function getLayoutTypes()
    {
        $layoutTypes = array();
        foreach ($this->layoutTypeRegistry->getLayoutTypes() as $layoutType) {
            if ($layoutType->isEnabled()) {
                $layoutTypes[] = new VersionedValue($layoutType, Version::API_V1);
            }
        }

        return new ValueArray($layoutTypes);
    }

    /**
     * Serializes the collection sources.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function getSources()
    {
        $sources = array();
        foreach ($this->sourceRegistry->getSources() as $source) {
            if ($source->isEnabled()) {
                $sources[] = new VersionedValue($source, Version::API_V1);
            }
        }

        return new ValueArray($sources);
    }
}
