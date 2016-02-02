<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\BlockManager\API\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\API\Service\Mapper as MapperInterface;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Exception\BadStateException;
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
     * @var \Netgen\BlockManager\API\Service\Mapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\Validator\LayoutValidator $layoutValidator
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\API\Service\Mapper $mapper
     */
    public function __construct(
        LayoutValidator $layoutValidator,
        Handler $persistenceHandler,
        MapperInterface $mapper
    ) {
        $this->layoutValidator = $layoutValidator;
        $this->persistenceHandler = $persistenceHandler;
        $this->mapper = $mapper;
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
    public function loadLayout($layoutId, $status = Layout::STATUS_PUBLISHED)
    {
        if (!is_int($layoutId) && !is_string($layoutId)) {
            throw new InvalidArgumentException('layoutId', 'Value must be an integer or a string.');
        }

        if (empty($layoutId)) {
            throw new InvalidArgumentException('layoutId', 'Value must not be empty.');
        }

        $layout = $this->persistenceHandler->getLayoutHandler()->loadLayout($layoutId, $status);

        return $this->mapper->mapLayout($layout);
    }

    /**
     * Loads a zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If layout ID or zone identifier have an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($layoutId, $identifier, $status = Layout::STATUS_PUBLISHED)
    {
        if (!is_int($layoutId) && !is_string($layoutId)) {
            throw new InvalidArgumentException('layoutId', 'Value must be an integer or a string.');
        }

        if (empty($layoutId)) {
            throw new InvalidArgumentException('layoutId', 'Value must not be empty.');
        }

        if (!is_string($identifier)) {
            throw new InvalidArgumentException('identifier', 'Value must be a string.');
        }

        if (empty($identifier)) {
            throw new InvalidArgumentException('identifier', 'Value must not be empty.');
        }

        $layoutHandler = $this->persistenceHandler->getLayoutHandler();

        $layout = $layoutHandler->loadLayout($layoutId);

        return $this->mapper->mapZone(
            $layoutHandler->loadZone(
                $layout->id,
                $identifier,
                $status
            )
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
    public function createLayout(LayoutCreateStruct $layoutCreateStruct, Layout $parentLayout = null)
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

        return $this->mapper->mapLayout($createdLayout);
    }

    /**
     * Copies a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function copyLayout(Layout $layout)
    {
        $layoutHandler = $this->persistenceHandler->getLayoutHandler();

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedLayout = $layoutHandler->copyLayout(
                $layout->getId(),
                true,
                Layout::STATUS_PUBLISHED,
                Layout::STATUS_DRAFT
            );

            foreach ($layout->getZones() as $zone) {
                foreach ($zone->getBlocks() as $block) {
                    $layoutHandler->copyBlock(
                        $block->getId(),
                        $copiedLayout->id,
                        $zone->getIdentifier(),
                        true,
                        Layout::STATUS_PUBLISHED,
                        Layout::STATUS_DRAFT
                    );
                }
            }
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapLayout($copiedLayout);
    }

    /**
     * Creates a new layout status.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayoutStatus(Layout $layout, $newStatus)
    {
        $layoutHandler = $this->persistenceHandler->getLayoutHandler();

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedLayout = $layoutHandler->copyLayout(
                $layout->getId(),
                false,
                $layout->getStatus(),
                $newStatus
            );

            foreach ($layout->getZones() as $zone) {
                foreach ($zone->getBlocks() as $block) {
                    $layoutHandler->copyBlock(
                        $block->getId(),
                        $layout->getId(),
                        $zone->getIdentifier(),
                        false,
                        $layout->getStatus(),
                        $newStatus
                    );
                }
            }
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapLayout($copiedLayout);
    }

    /**
     * Publishes a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function publishLayout(Layout $layout)
    {
        if ($layout->getStatus() !== Layout::STATUS_DRAFT) {
            throw new BadStateException('layout', 'Only layouts in draft status can be published.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $publishedLayout = $this->persistenceHandler->getLayoutHandler()->publishLayout(
                $layout->getId()
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapLayout($publishedLayout);
    }

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param int $status
     */
    public function deleteLayout(Layout $layout, $status = null)
    {
        $this->persistenceHandler->beginTransaction();

        $layoutHandler = $this->persistenceHandler->getLayoutHandler();

        try {
            $layoutHandler->deleteLayoutBlocks($layout->getId(), $status);
            $layoutHandler->deleteLayout($layout->getId(), $status);
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
     * @param string $name
     * @param string[] $zoneIdentifiers
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct($identifier, $name, array $zoneIdentifiers)
    {
        return new LayoutCreateStruct(
            array(
                'identifier' => $identifier,
                'name' => $name,
                'zoneIdentifiers' => $zoneIdentifiers,
            )
        );
    }
}
