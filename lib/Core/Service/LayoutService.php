<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\BlockManager\API\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Page\Zone as PersistenceZone;
use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use DateTime;
use Exception;

class LayoutService implements LayoutServiceInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\Validator\LayoutValidator
     */
    protected $layoutValidator;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\Validator\LayoutValidator $layoutValidator
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\BlockService $blockService
     */
    public function __construct(LayoutValidator $layoutValidator, Handler $persistenceHandler, BlockService $blockService)
    {
        $this->layoutValidator = $layoutValidator;
        $this->persistenceHandler = $persistenceHandler;
        $this->blockService = $blockService;
    }

    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If layout ID has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayout($layoutId, $status = APILayout::STATUS_PUBLISHED)
    {
        if (!is_int($layoutId) && !is_string($layoutId)) {
            throw new InvalidArgumentException('layoutId', 'Value must be an integer or a string.');
        }

        if (empty($layoutId)) {
            throw new InvalidArgumentException('layoutId', 'Value must not be empty.');
        }

        $layout = $this->persistenceHandler->getLayoutHandler()->loadLayout($layoutId, $status);

        return $this->buildDomainLayoutObject($layout);
    }

    /**
     * Loads a zone with specified ID.
     *
     * @param int|string $zoneId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If zone ID has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If zone with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($zoneId, $status = APILayout::STATUS_PUBLISHED)
    {
        if (!is_int($zoneId) && !is_string($zoneId)) {
            throw new InvalidArgumentException('zoneId', 'Value must be an integer or a string.');
        }

        if (empty($zoneId)) {
            throw new InvalidArgumentException('zoneId', 'Value must not be empty.');
        }

        return $this->buildDomainZoneObject(
            $this->persistenceHandler->getLayoutHandler()->loadZone($zoneId, $status)
        );
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Layout $parentLayout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct, APILayout $parentLayout = null)
    {
        $this->layoutValidator->validateLayoutCreateStruct($layoutCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdLayout = $this->persistenceHandler->getLayoutHandler()->createLayout(
                $layoutCreateStruct,
                $parentLayout !== null ? $parentLayout->getId() : null
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->buildDomainLayoutObject($createdLayout);
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
        $this->persistenceHandler->beginTransaction();

        try {
            $copiedLayout = $this->persistenceHandler->getLayoutHandler()->copyLayout($layout->getId());
            // @TODO Copy blocks and block items
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->buildDomainLayoutObject($copiedLayout);
    }

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param int $status
     */
    public function deleteLayout(APILayout $layout, $status = null)
    {
        $this->persistenceHandler->beginTransaction();

        try {
            // @TODO Delete blocks and block items

            $this->persistenceHandler->getLayoutHandler()->deleteLayout($layout->getId(), $status);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Creates a new layout create struct.
     *
     * @param string $identifier
     * @param string[] $zoneIdentifiers
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct($identifier, array $zoneIdentifiers, $name)
    {
        return new LayoutCreateStruct(
            array(
                'identifier' => $identifier,
                'zoneIdentifiers' => $zoneIdentifiers,
                'name' => $name,
            )
        );
    }

    /**
     * Builds the API layout value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $persistenceLayout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    protected function buildDomainLayoutObject(PersistenceLayout $persistenceLayout)
    {
        $zones = array();

        $persistenceZones = $this->persistenceHandler->getLayoutHandler()->loadLayoutZones(
            $persistenceLayout->id,
            $persistenceLayout->status
        );

        foreach ($persistenceZones as $persistenceZone) {
            $zones[$persistenceZone->identifier] = $this->buildDomainZoneObject($persistenceZone);
        }

        $layoutData = array(
            'id' => $persistenceLayout->id,
            'parentId' => $persistenceLayout->parentId,
            'identifier' => $persistenceLayout->identifier,
            'name' => $persistenceLayout->name,
            'created' => $this->createDateTime($persistenceLayout->created),
            'modified' => $this->createDateTime($persistenceLayout->modified),
            'status' => $persistenceLayout->status,
            'zones' => $zones,
        );

        return new Layout($layoutData);
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
        $tempZone = new Zone(array('id' => $persistenceZone->id));

        $zoneData = array(
            'id' => $persistenceZone->id,
            'layoutId' => $persistenceZone->layoutId,
            'identifier' => $persistenceZone->identifier,
            'status' => $persistenceZone->status,
            'blocks' => $this->blockService->loadZoneBlocks($tempZone, $persistenceZone->status),
        );

        return new Zone($zoneData);
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
