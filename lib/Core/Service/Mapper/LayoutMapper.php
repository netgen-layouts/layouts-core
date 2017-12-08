<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use DateTime;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\BlockManager\Persistence\Values\Value as PersistenceValue;

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
     * Builds the API zone value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function mapZone(PersistenceZone $zone)
    {
        $linkedZone = null;

        if ($zone->linkedLayoutId !== null && $zone->linkedZoneIdentifier !== null) {
            try {
                // We're always using published versions of linked zones
                $linkedZone = $this->layoutHandler->loadZone(
                    $zone->linkedLayoutId,
                    PersistenceValue::STATUS_PUBLISHED,
                    $zone->linkedZoneIdentifier
                );

                $linkedZone = $this->mapZone($linkedZone);
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        $zoneData = array(
            'identifier' => $zone->identifier,
            'layoutId' => $zone->layoutId,
            'status' => $zone->status,
            'linkedZone' => $linkedZone,
            'published' => $zone->status === Value::STATUS_PUBLISHED,
        );

        return new Zone($zoneData);
    }

    /**
     * Builds the API layout value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function mapLayout(PersistenceLayout $layout)
    {
        $persistenceZones = $this->layoutHandler->loadLayoutZones($layout);

        $zones = array();
        foreach ($persistenceZones as $persistenceZone) {
            $zones[$persistenceZone->identifier] = $this->mapZone($persistenceZone);
        }

        $layoutData = array(
            'id' => $layout->id,
            'layoutType' => $this->layoutTypeRegistry->getLayoutType(
                $layout->type
            ),
            'name' => $layout->name,
            'description' => $layout->description,
            'created' => $this->createDateTime($layout->created),
            'modified' => $this->createDateTime($layout->modified),
            'status' => $layout->status,
            'shared' => $layout->shared,
            'mainLocale' => $layout->mainLocale,
            'availableLocales' => $layout->availableLocales,
            'zones' => $zones,
            'published' => $layout->status === Value::STATUS_PUBLISHED,
        );

        return new Layout($layoutData);
    }

    /**
     * Builds and returns the \DateTime object from the provided timestamp.
     *
     * @param int $timestamp
     *
     * @return \DateTime
     */
    private function createDateTime($timestamp)
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp((int) $timestamp);

        return $dateTime;
    }
}
