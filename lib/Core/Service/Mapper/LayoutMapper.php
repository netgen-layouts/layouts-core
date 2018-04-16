<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\BlockManager\Core\Values\LazyCollection;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\BlockManager\Persistence\Values\Value as PersistenceValue;
use Netgen\BlockManager\Utils\DateTimeUtils;

final class LayoutMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    public function __construct(LayoutHandlerInterface $layoutHandler, LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        $this->layoutHandler = $layoutHandler;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Builds the API zone value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function mapZone(PersistenceZone $zone)
    {
        $zoneData = array(
            'identifier' => $zone->identifier,
            'layoutId' => $zone->layoutId,
            'status' => $zone->status,
            'linkedZone' => function () use ($zone) {
                if ($zone->linkedLayoutId === null || $zone->linkedZoneIdentifier === null) {
                    return;
                }

                try {
                    // We're always using published versions of linked zones
                    $linkedZone = $this->layoutHandler->loadZone(
                        $zone->linkedLayoutId,
                        PersistenceValue::STATUS_PUBLISHED,
                        $zone->linkedZoneIdentifier
                    );

                    return $this->mapZone($linkedZone);
                } catch (NotFoundException $e) {
                    return;
                }
            },
        );

        return new Zone($zoneData);
    }

    /**
     * Builds the API layout value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function mapLayout(PersistenceLayout $layout)
    {
        $layoutData = array(
            'id' => $layout->id,
            'layoutType' => $this->layoutTypeRegistry->getLayoutType($layout->type),
            'name' => $layout->name,
            'description' => $layout->description,
            'created' => DateTimeUtils::createFromTimestamp($layout->created),
            'modified' => DateTimeUtils::createFromTimestamp($layout->modified),
            'status' => $layout->status,
            'shared' => $layout->shared,
            'mainLocale' => $layout->mainLocale,
            'availableLocales' => $layout->availableLocales,
            'zones' => new LazyCollection(
                function () use ($layout) {
                    return array_map(
                        function (PersistenceZone $zone) {
                            return $this->mapZone($zone);
                        },
                        $this->layoutHandler->loadLayoutZones($layout)
                    );
                }
            ),
        );

        return new Layout($layoutData);
    }
}
