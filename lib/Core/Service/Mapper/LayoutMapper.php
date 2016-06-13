<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Persistence\Values\Page\Zone as PersistenceZone;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\ZoneDraft;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\LayoutDraft;
use Netgen\BlockManager\Persistence\Handler;

class LayoutMapper extends Mapper
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected $blockMapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\Mapper\BlockMapper $blockMapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(BlockMapper $blockMapper, Handler $persistenceHandler)
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
        $persistenceBlocks = $this->persistenceHandler->getBlockHandler()->loadZoneBlocks($zone);

        $blocks = array();
        foreach ($persistenceBlocks as $persistenceBlock) {
            $blocks[] = $this->blockMapper->mapBlock($persistenceBlock);
        }

        $zoneData = array(
            'identifier' => $zone->identifier,
            'layoutId' => $zone->layoutId,
            'status' => $zone->status,
            'blocks' => $blocks,
        );

        return $zone->status === PersistenceLayout::STATUS_PUBLISHED ?
            new Zone($zoneData) :
            new ZoneDraft($zoneData);
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
        $persistenceZones = $this->persistenceHandler->getLayoutHandler()->loadLayoutZones($layout);

        $zones = array();
        foreach ($persistenceZones as $persistenceZone) {
            $zones[$persistenceZone->identifier] = $this->mapZone($persistenceZone);
        }

        $layoutData = array(
            'id' => $layout->id,
            'type' => $layout->type,
            'name' => $layout->name,
            'created' => $this->createDateTime($layout->created),
            'modified' => $this->createDateTime($layout->modified),
            'status' => $layout->status,
            'zones' => $zones,
        );

        return $layout->status === PersistenceLayout::STATUS_PUBLISHED ?
            new Layout($layoutData) :
            new LayoutDraft($layoutData);
    }
}
