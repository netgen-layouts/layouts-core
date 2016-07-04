<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Values\LayoutUpdateStruct;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\LayoutDraft;
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
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayout($layoutId)
    {
        $this->layoutValidator->validateId($layoutId, 'layoutId');

        return $this->layoutMapper->mapLayout(
            $this->layoutHandler->loadLayout(
                $layoutId,
                Layout::STATUS_PUBLISHED
            )
        );
    }

    /**
     * Loads a layout draft with specified ID.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutDraft
     */
    public function loadLayoutDraft($layoutId)
    {
        return $this->layoutMapper->mapLayout(
            $this->layoutHandler->loadLayout(
                $layoutId,
                Layout::STATUS_DRAFT
            )
        );
    }

    /**
     * Returns if provided layout has a published status.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return bool
     */
    public function isPublished(Layout $layout)
    {
        return $this->layoutHandler->layoutExists($layout->getId(), Layout::STATUS_PUBLISHED);
    }

    /**
     * Loads a zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($layoutId, $identifier)
    {
        $this->layoutValidator->validateId($layoutId, 'layoutId');
        $this->layoutValidator->validateIdentifier($identifier, 'identifier');

        return $this->layoutMapper->mapZone(
            $this->layoutHandler->loadZone(
                $this->layoutHandler->loadLayout(
                    $layoutId,
                    Layout::STATUS_PUBLISHED
                ),
                $identifier
            )
        );
    }

    /**
     * Loads a zone draft with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\ZoneDraft
     */
    public function loadZoneDraft($layoutId, $identifier)
    {
        $this->layoutValidator->validateId($layoutId, 'layoutId');
        $this->layoutValidator->validateIdentifier($identifier, 'identifier');

        return $this->layoutMapper->mapZone(
            $this->layoutHandler->loadZone(
                $this->layoutHandler->loadLayout(
                    $layoutId,
                    Layout::STATUS_DRAFT
                ),
                $identifier
            )
        );
    }

    /**
     * Returns if layout with provided name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function layoutNameExists($name)
    {
        return $this->layoutHandler->layoutNameExists($name);
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If layout type does not exist
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutDraft
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct)
    {
        $this->layoutValidator->validateLayoutCreateStruct($layoutCreateStruct);

        if (!$this->layoutTypeRegistry->hasLayoutType($layoutCreateStruct->type)) {
            throw new InvalidArgumentException('layoutCreateStruct', 'Provided layout type does not exist.');
        }

        if ($this->layoutHandler->layoutNameExists($layoutCreateStruct->name)) {
            throw new BadStateException('name', 'Layout with provided name already exists.');
        }

        $layoutType = $this->layoutTypeRegistry->getLayoutType($layoutCreateStruct->type);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdLayout = $this->layoutHandler->createLayout(
                $layoutCreateStruct,
                Layout::STATUS_DRAFT,
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
     * Updates a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     * @param \Netgen\BlockManager\API\Values\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutDraft
     */
    public function updateLayout(LayoutDraft $layout, LayoutUpdateStruct $layoutUpdateStruct)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Layout::STATUS_DRAFT);

        $this->layoutValidator->validateLayoutUpdateStruct($layoutUpdateStruct);

        if ($layoutUpdateStruct->name !== null) {
            if ($this->layoutHandler->layoutNameExists($layoutUpdateStruct->name, $persistenceLayout->id)) {
                throw new BadStateException('name', 'Layout with provided name already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedLayout = $this->layoutHandler->updateLayout(
                $persistenceLayout,
                $layoutUpdateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->layoutMapper->mapLayout($updatedLayout);
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
            $copiedLayoutId = $this->layoutHandler->copyLayout(
                $persistenceLayout->id
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->layoutMapper->mapLayout(
            $this->layoutHandler->loadLayout($copiedLayoutId, $persistenceLayout->status)
        );
    }

    /**
     * Creates a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If draft already exists for layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutDraft
     */
    public function createDraft(Layout $layout)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Layout::STATUS_PUBLISHED);

        if ($this->layoutHandler->layoutExists($persistenceLayout->id, Layout::STATUS_DRAFT)) {
            throw new BadStateException('layout', 'The provided layout already has a draft.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->layoutHandler->deleteLayout($persistenceLayout->id, Layout::STATUS_DRAFT);
            $layoutDraft = $this->layoutHandler->createLayoutStatus($persistenceLayout, Layout::STATUS_DRAFT);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->layoutMapper->mapLayout($layoutDraft);
    }

    /**
     * Discards a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     */
    public function discardDraft(LayoutDraft $layout)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Layout::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->layoutHandler->deleteLayout(
                $persistenceLayout->id,
                Layout::STATUS_DRAFT
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Publishes a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function publishLayout(LayoutDraft $layout)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Layout::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->layoutHandler->deleteLayout($persistenceLayout->id, Layout::STATUS_ARCHIVED);

            if ($this->layoutHandler->layoutExists($persistenceLayout->id, Layout::STATUS_PUBLISHED)) {
                $this->layoutHandler->createLayoutStatus(
                    $this->layoutHandler->loadLayout($persistenceLayout->id, Layout::STATUS_PUBLISHED),
                    Layout::STATUS_ARCHIVED
                );

                $this->layoutHandler->deleteLayout($persistenceLayout->id, Layout::STATUS_PUBLISHED);
            }

            $publishedLayout = $this->layoutHandler->createLayoutStatus($persistenceLayout, Layout::STATUS_PUBLISHED);
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
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     */
    public function deleteLayout(Layout $layout)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), $layout->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->layoutHandler->deleteLayout(
                $persistenceLayout->id
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

    /**
     * Creates a new layout update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutUpdateStruct
     */
    public function newLayoutUpdateStruct()
    {
        return new LayoutUpdateStruct();
    }
}
