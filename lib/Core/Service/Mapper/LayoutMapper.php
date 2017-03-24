<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\BlockManager\Persistence\Values\Value as PersistenceValue;

class LayoutMapper extends Mapper
{
    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     */
    public function __construct(Handler $persistenceHandler, LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        parent::__construct($persistenceHandler);

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
                $linkedZone = $this->persistenceHandler->getLayoutHandler()->loadZone(
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
        $persistenceZones = $this->persistenceHandler->getLayoutHandler()->loadLayoutZones($layout);

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
            'created' => $this->createDateTime($layout->created),
            'modified' => $this->createDateTime($layout->modified),
            'status' => $layout->status,
            'shared' => $layout->shared,
            'zones' => $zones,
            'published' => $layout->status === Value::STATUS_PUBLISHED,
        );

        return new Layout($layoutData);
    }
}
