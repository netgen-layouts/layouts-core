<?php

namespace Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Configuration\Registry\BlockTypeRegistryInterface;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BlockTypeGroupNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistryInterface
     */
    protected $blockTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistryInterface $blockTypeRegistry
     */
    public function __construct(BlockTypeRegistryInterface $blockTypeRegistry)
    {
        $this->blockTypeRegistry = $blockTypeRegistry;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\Values\VersionedValue $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup $blockTypeGroup */
        $blockTypeGroup = $object->getValue();

        $enabledBlockTypes = array();
        foreach ($blockTypeGroup->getBlockTypes() as $blockType) {
            if ($this->blockTypeRegistry->getBlockType($blockType)->isEnabled()) {
                $enabledBlockTypes[] = $blockType;
            }
        }

        return array(
            'identifier' => $blockTypeGroup->getIdentifier(),
            'name' => $blockTypeGroup->getName(),
            'block_types' => $enabledBlockTypes,
        );
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data
     * @param string $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof BlockTypeGroup && $data->getVersion() === Version::API_V1;
    }
}
