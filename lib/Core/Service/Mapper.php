<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\Mapper as MapperInterface;
use Netgen\BlockManager\Persistence\Values\Page\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Page\Zone as PersistenceZone;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Handler;
use DateTime;

class Mapper implements MapperInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(Handler $persistenceHandler)
    {
        $this->persistenceHandler = $persistenceHandler;
    }

    /**
     * Builds the API block value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function mapBlock(PersistenceBlock $block)
    {
        return new Block(
            array(
                'id' => $block->id,
                'layoutId' => $block->layoutId,
                'zoneIdentifier' => $block->zoneIdentifier,
                'definitionIdentifier' => $block->definitionIdentifier,
                'parameters' => $block->parameters,
                'viewType' => $block->viewType,
                'name' => $block->name,
                'status' => $block->status,
            )
        );
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
            $blocks[] = $this->mapBlock($persistenceBlock);
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
