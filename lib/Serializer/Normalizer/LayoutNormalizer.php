<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Serializer\SerializableValue;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LayoutNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer
     */
    protected $blockNormalizer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     * @param \Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer $blockNormalizer
     */
    public function __construct(
        ConfigurationInterface $configuration,
        BlockNormalizer $blockNormalizer
    ) {
        $this->configuration = $configuration;
        $this->blockNormalizer = $blockNormalizer;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\SerializableValue $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $layout = $object->getValue();

        return array(
            'id' => $layout->getId(),
            'parent_id' => $layout->getParentId(),
            'identifier' => $layout->getIdentifier(),
            'created_at' => $layout->getCreated(),
            'updated_at' => $layout->getModified(),
            'name' => $layout->getName(),
            'zones' => $this->getZones($layout),
            'blocks' => $this->normalizeBlocks($layout, $object->getVersion()),
            'positions' => $this->getBlockPositions($layout),
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
        if (!$data instanceof SerializableValue) {
            return false;
        }

        return $data->getValue() instanceof Layout && $data->getVersion() === 1;
    }

    /**
     * Returns the data for blocks contained within the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param int $version
     *
     * @return array
     */
    protected function normalizeBlocks(Layout $layout, $version)
    {
        $blocks = array();

        foreach ($layout->getZones() as $zone) {
            $blocks = array_merge($blocks, $zone->getBlocks());
        }

        $normalizedBlocks = array();
        foreach ($blocks as $block) {
            $normalizedBlocks[] = $this->blockNormalizer->normalize(
                new SerializableValue($block, $version)
            );
        }

        return $normalizedBlocks;
    }

    /**
     * Returns the array with layout zones.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return array
     */
    protected function getZones(Layout $layout)
    {
        $zones = array();
        $layoutConfig = $this->configuration->getLayoutConfig($layout->getIdentifier());

        foreach ($layout->getZones() as $zoneIdentifier => $zone) {
            $allowedBlocks = true;

            if (isset($layoutConfig['zones'][$zoneIdentifier])) {
                $zoneConfig = $layoutConfig['zones'][$zoneIdentifier];
                if (!empty($zoneConfig['allowed_blocks'])) {
                    $allowedBlocks = $zoneConfig['allowed_blocks'];
                }
            }

            $zones[] = array(
                'identifier' => $zoneIdentifier,
                'allowed_blocks' => $allowedBlocks,
            );
        }

        return $zones;
    }

    /**
     * Returns the array with block positions inside zones.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return array
     */
    protected function getBlockPositions(Layout $layout)
    {
        $positions = array();

        foreach ($layout->getZones() as $zoneIdentifier => $zone) {
            $positions[] = array(
                'zone' => $zoneIdentifier,
                'blocks' => array_map(
                    function (Block $block) {
                        return $block->getId();
                    },
                    $zone->getBlocks()
                ),
            );
        }

        return $positions;
    }
}
