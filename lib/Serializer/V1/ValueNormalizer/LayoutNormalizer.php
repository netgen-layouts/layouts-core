<?php

namespace Netgen\BlockManager\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\LayoutInfo;
use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use DateTime;

class LayoutNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
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
        /** @var \Netgen\BlockManager\API\Values\Page\Layout $layout */
        $layout = $object->getValue();
        $layoutType = $layout->getLayoutType();

        $data = array(
            'id' => $layout->getId(),
            'type' => $layoutType->getIdentifier(),
            'published' => $layout->getStatus() === Layout::STATUS_PUBLISHED ?
                true :
                false,
            'has_published_state' => $this->layoutService->isPublished($layout),
            'created_at' => $layout->getCreated()->format(DateTime::ISO8601),
            'updated_at' => $layout->getModified()->format(DateTime::ISO8601),
            'shared' => $layout->isShared(),
            'name' => $layout->getName(),
        );

        if ($layout instanceof Layout) {
            $data['zones'] = $this->getZones($layout, $layoutType);
        }

        return $data;
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

        return $data->getValue() instanceof LayoutInfo && $data->getVersion() === Version::API_V1;
    }

    /**
     * Returns the array with layout zones.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param \Netgen\BlockManager\Configuration\LayoutType\LayoutType $layoutType
     *
     * @return array
     */
    protected function getZones(Layout $layout, LayoutType $layoutType)
    {
        $zones = array();

        foreach ($layout->getZones() as $zoneIdentifier => $zone) {
            $linkedZone = $this->layoutService->findLinkedZone($zone);

            $zones[] = array(
                'identifier' => $zoneIdentifier,
                'name' => $this->getZoneName($zone, $layoutType),
                'block_ids' => array_map(
                    function (Block $block) {
                        return $block->getId();
                    },
                    $zone->getBlocks()
                ),
                'allowed_block_definitions' => $this->getAllowedBlocks(
                    $zone,
                    $layoutType
                ),
                'linked_layout_id' => $linkedZone ? $linkedZone->getLayoutId() : null,
                'linked_zone_identifier' => $linkedZone ? $linkedZone->getIdentifier() : null,
            );
        }

        return $zones;
    }

    /**
     * Returns provided zone name.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param \Netgen\BlockManager\Configuration\LayoutType\LayoutType $layoutType
     *
     * @return string
     */
    protected function getZoneName(Zone $zone, LayoutType $layoutType)
    {
        if ($layoutType->hasZone($zone->getIdentifier())) {
            return $layoutType->getZone($zone->getIdentifier())->getName();
        }

        return '';
    }

    /**
     * Returns all allowed block definitions from provided zone or
     * true if all block definitions are allowed.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param \Netgen\BlockManager\Configuration\LayoutType\LayoutType $layoutType
     *
     * @return array|bool
     */
    protected function getAllowedBlocks(Zone $zone, LayoutType $layoutType)
    {
        if ($layoutType->hasZone($zone->getIdentifier())) {
            $layoutTypeZone = $layoutType->getZone($zone->getIdentifier());
            if (!empty($layoutTypeZone->getAllowedBlockDefinitions())) {
                return $layoutTypeZone->getAllowedBlockDefinitions();
            }
        }

        return true;
    }
}
