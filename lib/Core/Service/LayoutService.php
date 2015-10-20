<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\BlockManager\Persistence\Handler\Layout as LayoutHandler;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Page\Zone as PersistenceZone;
use DateTime;

class LayoutService implements LayoutServiceInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\Layout
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler\Layout $handler
     */
    public function __construct(LayoutHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayout($layoutId)
    {
        $layout = $this->handler->loadLayout($layoutId);
        $zones = $this->handler->loadLayoutZones($layout->id);

        return $this->buildDomainLayoutObject($layout, $zones);
    }

    /**
     * Loads a zone with specified ID.
     *
     * @param int|string $zoneId
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($zoneId)
    {
        return $this->buildDomainZoneObject(
            $this->handler->loadZone($zoneId)
        );
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct)
    {
        $createdLayout = $this->handler->createLayout($layoutCreateStruct);
        $zones = $this->handler->loadLayoutZones($createdLayout->id);

        return $this->buildDomainLayoutObject($createdLayout, $zones);
    }

    /**
     * Copies a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function copyLayout(APILayout $layout)
    {
        $copiedLayout = $this->handler->copyLayout($layout->getId());
        $zones = $this->handler->loadLayoutZones($copiedLayout->id);

        // @TODO Copy blocks and block items

        return $this->buildDomainLayoutObject($copiedLayout, $zones);
    }

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     */
    public function deleteLayout(APILayout $layout)
    {
        // @TODO Delete blocks and block items

        $this->handler->deleteLayout($layout->getId());
    }

    /**
     * Creates a new layout create struct.
     *
     * @param string $layoutIdentifier
     * @param string[] $zoneIdentifiers
     * @param int|string $parentId
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct(
        $layoutIdentifier,
        $zoneIdentifiers = array(),
        $parentId = null
    ) {
        return new LayoutCreateStruct(
            array(
                'parentId' => $parentId,
                'layoutIdentifier' => $layoutIdentifier,
                'zoneIdentifiers' => $zoneIdentifiers,
            )
        );
    }

    /**
     * Builds the API layout value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $persistenceLayout
     * @param array $persistenceZones
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    protected function buildDomainLayoutObject(
        PersistenceLayout $persistenceLayout,
        array $persistenceZones = array()
    ) {
        $zones = array();

        foreach ($persistenceZones as $persistenceZone) {
            $zones[] = $this->buildDomainZoneObject($persistenceZone);
        }

        $layout = new Layout(
            array(
                'id' => $persistenceLayout->id,
                'parentId' => $persistenceLayout->parentId,
                'identifier' => $persistenceLayout->identifier,
                'created' => $this->createDateTime($persistenceLayout->created),
                'modified' => $this->createDateTime($persistenceLayout->modified),
                'zones' => $zones,
            )
        );

        return $layout;
    }

    /**
     * Builds the API zone value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Zone $persistenceZone
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    protected function buildDomainZoneObject(PersistenceZone $persistenceZone)
    {
        $zone = new Zone(
            array(
                'id' => $persistenceZone->id,
                'layoutId' => $persistenceZone->layoutId,
                'identifier' => $persistenceZone->identifier,
            )
        );

        return $zone;
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
