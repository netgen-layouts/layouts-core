<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use DateTime;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class LayoutNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(LayoutService $layoutService, BlockService $blockService)
    {
        $this->layoutService = $layoutService;
        $this->blockService = $blockService;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\API\Values\Layout\Layout $layout */
        $layout = $object->getValue();
        $layoutType = $layout->getLayoutType();
        $localeBundle = Intl::getLocaleBundle();

        $availableLocales = [];
        foreach ($layout->getAvailableLocales() as $locale) {
            $availableLocales[$locale] = $localeBundle->getLocaleName($locale);
        }

        $data = [
            'id' => $layout->getId(),
            'type' => $layoutType->getIdentifier(),
            'published' => $layout->isPublished(),
            'has_published_state' => $this->layoutService->hasPublishedState($layout),
            'created_at' => $layout->getCreated()->format(DateTime::ISO8601),
            'updated_at' => $layout->getModified()->format(DateTime::ISO8601),
            'shared' => $layout->isShared(),
            'name' => $layout->getName(),
            'description' => $layout->getDescription(),
            'main_locale' => $layout->getMainLocale(),
            'available_locales' => $availableLocales,
            'zones' => $this->getZones($layout, $layoutType),
        ];

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Layout && $data->getVersion() === Version::API_V1;
    }

    /**
     * Returns the array with layout zones.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     *
     * @return array
     */
    private function getZones(Layout $layout, LayoutType $layoutType)
    {
        $zones = [];

        foreach ($layout as $zoneIdentifier => $zone) {
            $linkedZone = $zone->getLinkedZone();

            $zones[] = [
                'identifier' => $zoneIdentifier,
                'name' => $this->getZoneName($zone, $layoutType),
                'block_ids' => array_map(
                    function (Block $block) {
                        return $block->getId();
                    },
                    $this->blockService->loadZoneBlocks($zone)
                ),
                'allowed_block_definitions' => $this->getAllowedBlocks(
                    $zone,
                    $layoutType
                ),
                'linked_layout_id' => $linkedZone ? $linkedZone->getLayoutId() : null,
                'linked_zone_identifier' => $linkedZone ? $linkedZone->getIdentifier() : null,
            ];
        }

        return $zones;
    }

    /**
     * Returns provided zone name.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     *
     * @return string
     */
    private function getZoneName(Zone $zone, LayoutType $layoutType)
    {
        if ($layoutType->hasZone($zone->getIdentifier())) {
            return $layoutType->getZone($zone->getIdentifier())->getName();
        }

        return $zone->getIdentifier();
    }

    /**
     * Returns all allowed block definitions from provided zone or
     * true if all block definitions are allowed.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     *
     * @return array|bool
     */
    private function getAllowedBlocks(Zone $zone, LayoutType $layoutType)
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
