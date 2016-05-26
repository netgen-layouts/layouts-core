<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Exception\BadStateException;
use Exception;

class LayoutService implements LayoutServiceInterface
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\LayoutValidator
     */
    protected $layoutValidator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandler
     */
    protected $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $layoutValidator
     * @param \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper $layoutMapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     */
    public function __construct(
        LayoutValidator $layoutValidator,
        LayoutMapper $layoutMapper,
        Handler $persistenceHandler,
        LayoutTypeRegistryInterface $layoutTypeRegistry
    ) {
        $this->layoutValidator = $layoutValidator;
        $this->layoutMapper = $layoutMapper;
        $this->persistenceHandler = $persistenceHandler;
        $this->layoutTypeRegistry = $layoutTypeRegistry;

        $this->layoutHandler = $persistenceHandler->getLayoutHandler();
    }

    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayout($layoutId, $status = Layout::STATUS_PUBLISHED)
    {
        $this->layoutValidator->validateId($layoutId, 'layoutId');

        return $this->layoutMapper->mapLayout(
            $this->layoutHandler->loadLayout(
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
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($layoutId, $identifier, $status = Layout::STATUS_PUBLISHED)
    {
        $this->layoutValidator->validateId($layoutId, 'layoutId');
        $this->layoutValidator->validateIdentifier($identifier, 'identifier');

        return $this->layoutMapper->mapZone(
            $this->layoutHandler->loadZone(
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
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If layout type does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct)
    {
        $this->layoutValidator->validateLayoutCreateStruct($layoutCreateStruct);

        if (!$this->layoutTypeRegistry->hasLayoutType($layoutCreateStruct->type)) {
            throw new InvalidArgumentException('layoutCreateStruct', 'Provided layout type does not exist.');
        }

        $layoutType = $this->layoutTypeRegistry->getLayoutType($layoutCreateStruct->type);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdLayout = $this->layoutHandler->createLayout(
                $layoutCreateStruct,
                $layoutType->getZoneIdentifiers()
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
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), $layout->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedLayout = $this->layoutHandler->copyLayout(
                $persistenceLayout->id
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->layoutMapper->mapLayout($copiedLayout);
    }

    /**
     * Creates a new layout status.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout already has the provided status
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayoutStatus(Layout $layout, $status)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), $layout->getStatus());

        if ($this->layoutHandler->layoutExists($persistenceLayout->id, $status)) {
            throw new BadStateException('status', 'Layout already has the provided status.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdLayout = $this->layoutHandler->createLayoutStatus(
                $persistenceLayout->id,
                $persistenceLayout->status,
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
     * Creates a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not published
     *                                                              If draft already exists for layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createDraft(Layout $layout)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), $layout->getStatus());

        if ($persistenceLayout->status !== Layout::STATUS_PUBLISHED) {
            throw new BadStateException('layout', 'Drafts can be created only from published layouts.');
        }

        if ($this->layoutHandler->layoutExists($persistenceLayout->id, Layout::STATUS_DRAFT)) {
            throw new BadStateException('layout', 'The provided layout already has a draft.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->layoutHandler->deleteLayout($persistenceLayout->id, Layout::STATUS_DRAFT);
            $layoutDraft = $this->layoutHandler->createLayoutStatus($persistenceLayout->id, Layout::STATUS_PUBLISHED, Layout::STATUS_DRAFT);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->layoutMapper->mapLayout($layoutDraft);
    }

    /**
     * Publishes a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function publishLayout(Layout $layout)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), $layout->getStatus());

        if ($persistenceLayout->status !== Layout::STATUS_DRAFT) {
            throw new BadStateException('layout', 'Only layouts in draft status can be published.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->layoutHandler->deleteLayout($persistenceLayout->id, Layout::STATUS_ARCHIVED);

            if ($this->layoutHandler->layoutExists($persistenceLayout->id, Layout::STATUS_PUBLISHED)) {
                $this->layoutHandler->createLayoutStatus($persistenceLayout->id, Layout::STATUS_PUBLISHED, Layout::STATUS_ARCHIVED);
                $this->layoutHandler->deleteLayout($persistenceLayout->id, Layout::STATUS_PUBLISHED);
            }

            $publishedLayout = $this->layoutHandler->createLayoutStatus($persistenceLayout->id, Layout::STATUS_DRAFT, Layout::STATUS_PUBLISHED);
            $this->layoutHandler->deleteLayout($persistenceLayout->id, Layout::STATUS_DRAFT);
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
     * If $deleteAllStatuses is set to true, layout is completely deleted.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param bool $deleteAllStatuses
     */
    public function deleteLayout(Layout $layout, $deleteAllStatuses = false)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), $layout->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->layoutHandler->deleteLayout(
                $persistenceLayout->id,
                $deleteAllStatuses ? null : $persistenceLayout->status
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
     * @param string $type
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct($type, $name)
    {
        return new LayoutCreateStruct(
            array(
                'type' => $type,
                'name' => $name,
            )
        );
    }
}
