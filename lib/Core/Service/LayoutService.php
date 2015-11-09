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
use Netgen\BlockManager\Exceptions\InvalidArgumentException;
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
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If layout ID has an invalid or empty value
     * @throws \Netgen\BlockManager\Exceptions\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayout($layoutId)
    {
        if (!is_int($layoutId) && !is_string($layoutId)) {
            throw new InvalidArgumentException('layoutId', $layoutId, 'Value must be an integer or a string.');
        }

        if (empty($layoutId)) {
            throw new InvalidArgumentException('layoutId', $layoutId, 'Value must not be empty.');
        }

        $layout = $this->handler->loadLayout($layoutId);
        $zones = $this->handler->loadLayoutZones($layout->id);

        return $this->buildDomainLayoutObject($layout, $zones);
    }

    /**
     * Loads a zone with specified ID.
     *
     * @param int|string $zoneId
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If zone ID has an invalid or empty value
     * @throws \Netgen\BlockManager\Exceptions\NotFoundException If zone with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($zoneId)
    {
        if (!is_int($zoneId) && !is_string($zoneId)) {
            throw new InvalidArgumentException('zoneId', $zoneId, 'Value must be an integer or a string.');
        }

        if (empty($zoneId)) {
            throw new InvalidArgumentException('zoneId', $zoneId, 'Value must not be empty.');
        }

        return $this->buildDomainZoneObject(
            $this->handler->loadZone($zoneId)
        );
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Layout $parentLayout
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If create struct properties have an invalid or empty value
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct, APILayout $parentLayout = null)
    {
        if (!is_string($layoutCreateStruct->layoutIdentifier)) {
            throw new InvalidArgumentException(
                'layoutCreateStruct->layoutIdentifier',
                $layoutCreateStruct->layoutIdentifier, 'Value must be a string.');
        }

        if (empty($layoutCreateStruct->layoutIdentifier)) {
            throw new InvalidArgumentException(
                'layoutCreateStruct->layoutIdentifier',
                $layoutCreateStruct->layoutIdentifier,
                'Value must not be empty.'
            );
        }

        if (empty($layoutCreateStruct->zoneIdentifiers)) {
            throw new InvalidArgumentException(
                'layoutCreateStruct->zoneIdentifiers',
                '',
                'Value must not be empty.'
            );
        }

        foreach ($layoutCreateStruct->zoneIdentifiers as $zoneIdentifier) {
            if (!is_string($zoneIdentifier)) {
                throw new InvalidArgumentException(
                    'layoutCreateStruct->zoneIdentifiers',
                    $layoutCreateStruct->zoneIdentifiers,
                    'All values must be strings.'
                );
            }

            if (empty($zoneIdentifier)) {
                throw new InvalidArgumentException(
                    'layoutCreateStruct->zoneIdentifiers',
                    $layoutCreateStruct->zoneIdentifiers,
                    'None of the values can be empty.'
                );
            }
        }

        $createdLayout = $this->handler->createLayout(
            $layoutCreateStruct,
            $parentLayout !== null ? $parentLayout->getId() : null
        );
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
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct($layoutIdentifier, array $zoneIdentifiers)
    {
        return new LayoutCreateStruct(
            array(
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
