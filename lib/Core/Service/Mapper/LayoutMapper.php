<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Service\Mapper\BlockMapper as APIBlockMapper;
use Netgen\BlockManager\API\Service\Mapper\LayoutMapper as LayoutMapperInterface;
use Netgen\BlockManager\Persistence\Values\Page\Zone as PersistenceZone;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Handler;
use DateTime;

class LayoutMapper extends Mapper implements LayoutMapperInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\Mapper\BlockMapper
     */
    protected $blockMapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\Mapper\BlockMapper $blockMapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(APIBlockMapper $blockMapper, Handler $persistenceHandler)
    {
        parent::__construct($persistenceHandler);

        $this->blockMapper = $blockMapper;
    }

    /**
     * Builds the API zone value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function mapZone(PersistenceZone $zone)
    {
        $persistenceBlocks = $this->persistenceHandler->getLayoutHandler()->loadZoneBlocks(
            $zone->layoutId,
            $zone->identifier,
            $zone->status
        );

        $blocks = array();
        foreach ($persistenceBlocks as $persistenceBlock) {
            $blocks[] = $this->blockMapper->mapBlock($persistenceBlock);
        }

        return new Zone(
            array(
                'identifier' => $zone->identifier,
                'layoutId' => $zone->layoutId,
                'status' => $zone->status,
                'blocks' => $blocks,
            )
        );
    }

    /**
     * Builds the API layout value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function mapLayout(PersistenceLayout $layout)
    {
        $persistenceZones = $this->persistenceHandler->getLayoutHandler()->loadLayoutZones(
            $layout->id,
            $layout->status
        );

        $zones = array();
        foreach ($persistenceZones as $persistenceZone) {
            $zones[$persistenceZone->identifier] = $this->mapZone($persistenceZone);
        }

        return new Layout(
            array(
                'id' => $layout->id,
                'parentId' => $layout->parentId,
                'identifier' => $layout->identifier,
                'name' => $layout->name,
                'created' => $this->createDateTime($layout->created),
                'modified' => $this->createDateTime($layout->modified),
                'status' => $layout->status,
                'zones' => $zones,
            )
        );
    }

    /**
     * Returns \DateTime object from the timestamp.
     *
     * @param int $timestamp
     *
     * @return \DateTime
     */
    protected function createDateTime($timestamp)
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp((int)$timestamp);

        return $dateTime;
    }
}
