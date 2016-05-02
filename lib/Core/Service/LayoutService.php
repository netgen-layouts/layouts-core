<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\BlockManager\API\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\API\Service\Mapper\LayoutMapper;
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
     * @var \Netgen\BlockManager\API\Service\Mapper\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\Validator\LayoutValidator $layoutValidator
     * @param \Netgen\BlockManager\API\Service\Mapper\LayoutMapper $layoutMapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(
        LayoutValidator $layoutValidator,
        LayoutMapper $layoutMapper,
        Handler $persistenceHandler
    ) {
        $this->layoutValidator = $layoutValidator;
        $this->layoutMapper = $layoutMapper;
        $this->persistenceHandler = $persistenceHandler;
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

        return $this->layoutMapper->mapLayout(
            $this->persistenceHandler->getLayoutHandler()->loadLayout(
                $layoutId,
                $status
            )
        );
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

        return $this->layoutMapper->mapZone(
            $this->persistenceHandler->getLayoutHandler()->loadZone(
                $layoutId,
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

        return $this->layoutMapper->mapLayout($createdLayout);
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
    }

    /**
     * Creates a new layout status.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param int $status
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayoutStatus(Layout $layout, $status)
    {
        $this->persistenceHandler->beginTransaction();

        try {
            $createdLayout = $this->persistenceHandler->getLayoutHandler()->createLayoutStatus(
                $layout->getId(),
                $layout->getStatus(),
                $status
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->layoutMapper->mapLayout($createdLayout);
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
            $layoutHandler = $this->persistenceHandler->getLayoutHandler();
            $layoutHandler->deleteLayout($layout->getId(), Layout::STATUS_ARCHIVED);
            $layoutHandler->updateLayoutStatus($layout->getId(), Layout::STATUS_PUBLISHED, Layout::STATUS_ARCHIVED);
            $publishedLayout = $layoutHandler->updateLayoutStatus($layout->getId(), Layout::STATUS_DRAFT, Layout::STATUS_PUBLISHED);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->layoutMapper->mapLayout($publishedLayout);
    }

    /**
     * Deletes a specified layout.
     *
     * If $deleteAll is set to true, layout is completely deleted (i.e. all statuses).
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param bool $deleteAll
     */
    public function deleteLayout(Layout $layout, $deleteAll = false)
    {
        $this->persistenceHandler->beginTransaction();

        try {
            $this->persistenceHandler->getLayoutHandler()->deleteLayout(
                $layout->getId(),
                $deleteAll ? null : $layout->getStatus()
            );
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
