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
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
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
            $allowedBlocktypes = true;

            if (isset($layoutConfig['zones'][$zoneIdentifier])) {
                $zoneConfig = $layoutConfig['zones'][$zoneIdentifier];
                if (!empty($zoneConfig['allowed_block_types'])) {
                    $allowedBlocktypes = $zoneConfig['allowed_block_types'];
                }
            }

            $zones[] = array(
                'identifier' => $zoneIdentifier,
                'allowed_block_types' => $allowedBlocktypes,
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
