<?php

namespace Netgen\BlockManager\Core\Service;

use Exception;
use Netgen\BlockManager\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct as APILayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct as APILayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct;

class LayoutService extends Service implements LayoutServiceInterface
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\LayoutValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected $mapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder
     */
    protected $structBuilder;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandler
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     * @param \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper $mapper
     * @param \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder $structBuilder
     */
    public function __construct(
        Handler $persistenceHandler,
        LayoutValidator $validator,
        LayoutMapper $mapper,
        LayoutStructBuilder $structBuilder
    ) {
        parent::__construct($persistenceHandler);

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;

        $this->handler = $persistenceHandler->getLayoutHandler();
    }

    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function loadLayout($layoutId)
    {
        $this->validator->validateId($layoutId, 'layoutId');

        return $this->mapper->mapLayout(
            $this->handler->loadLayout(
                $layoutId,
                Value::STATUS_PUBLISHED
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
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function loadLayoutDraft($layoutId)
    {
        $this->validator->validateId($layoutId, 'layoutId');

        return $this->mapper->mapLayout(
            $this->handler->loadLayout(
                $layoutId,
                Value::STATUS_DRAFT
            )
        );
    }

    /**
     * Loads all layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param bool $includeDrafts
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    public function loadLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $this->validator->validateOffsetAndLimit($offset, $limit);

        $persistenceLayouts = $this->handler->loadLayouts(
            $includeDrafts,
            $offset,
            $limit
        );

        $layouts = array();
        foreach ($persistenceLayouts as $persistenceLayout) {
            $layouts[] = $this->mapper->mapLayout($persistenceLayout);
        }

        return $layouts;
    }

    /**
     * Loads all shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param bool $includeDrafts
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    public function loadSharedLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $this->validator->validateOffsetAndLimit($offset, $limit);

        $persistenceLayouts = $this->handler->loadSharedLayouts(
            $includeDrafts,
            $offset,
            $limit
        );

        $layouts = array();
        foreach ($persistenceLayouts as $persistenceLayout) {
            $layouts[] = $this->mapper->mapLayout($persistenceLayout);
        }

        return $layouts;
    }

    /**
     * Loads all layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $sharedLayout
     * @param int $offset
     * @param int $limit
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If provided layout is not shared
     *                                                          If provided layout is not published
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    public function loadRelatedLayouts(Layout $sharedLayout, $offset = 0, $limit = null)
    {
        if (!$sharedLayout->isPublished()) {
            throw new BadStateException('sharedLayout', 'Related layouts can only be loaded for published shared layouts.');
        }

        if (!$sharedLayout->isShared()) {
            throw new BadStateException('sharedLayout', 'Related layouts can only be loaded for shared layouts.');
        }

        $persistenceLayout = $this->handler->loadLayout($sharedLayout->getId(), $sharedLayout->getStatus());

        $relatedPersistenceLayouts = $this->handler->loadRelatedLayouts(
            $persistenceLayout,
            $offset,
            $limit
        );

        $relatedLayouts = array();
        foreach ($relatedPersistenceLayouts as $relatedPersistenceLayout) {
            $relatedLayouts[] = $this->mapper->mapLayout($relatedPersistenceLayout);
        }

        return $relatedLayouts;
    }

    /**
     * Returns if provided layout has a published status.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return bool
     */
    public function hasPublishedState(Layout $layout)
    {
        return $this->handler->layoutExists($layout->getId(), Value::STATUS_PUBLISHED);
    }

    /**
     * Loads a zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function loadZone($layoutId, $identifier)
    {
        $this->validator->validateId($layoutId, 'layoutId');
        $this->validator->validateIdentifier($identifier, 'identifier', true);

        return $this->mapper->mapZone(
            $this->handler->loadZone(
                $layoutId,
                Value::STATUS_PUBLISHED,
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
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function loadZoneDraft($layoutId, $identifier)
    {
        $this->validator->validateId($layoutId, 'layoutId');
        $this->validator->validateIdentifier($identifier, 'identifier', true);

        return $this->mapper->mapZone(
            $this->handler->loadZone(
                $layoutId,
                Value::STATUS_DRAFT,
                $identifier
            )
        );
    }

    /**
     * Returns if layout with provided name exists.
     *
     * @param string $name
     * @param int|string $excludedLayoutId
     *
     * @return bool
     */
    public function layoutNameExists($name, $excludedLayoutId = null)
    {
        return $this->handler->layoutNameExists($name, $excludedLayoutId);
    }

    /**
     * Links the zone to provided linked zone. If zone had a previous link, it will be overwritten.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $linkedZone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked zone is not published
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is in the shared layout
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked zone is not in the shared layout
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone and linked zone belong to the same layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function linkZone(Zone $zone, Zone $linkedZone)
    {
        if ($zone->isPublished()) {
            throw new BadStateException('zone', 'Only draft zones can be linked.');
        }

        if (!$linkedZone->isPublished()) {
            throw new BadStateException('linkedZone', 'Zones can only be linked to published zones.');
        }

        $persistenceLayout = $this->handler->loadLayout($zone->getLayoutId(), Value::STATUS_DRAFT);
        $persistenceZone = $this->handler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());

        $persistenceLinkedLayout = $this->handler->loadLayout($linkedZone->getLayoutId(), Value::STATUS_PUBLISHED);
        $persistenceLinkedZone = $this->handler->loadZone($linkedZone->getLayoutId(), Value::STATUS_PUBLISHED, $linkedZone->getIdentifier());

        if ($persistenceLayout->shared) {
            throw new BadStateException('zone', 'Zone cannot be in the shared layout.');
        }

        if ($persistenceZone->layoutId === $persistenceLinkedZone->layoutId) {
            throw new BadStateException('linkedZone', 'Linked zone needs to be in a different layout.');
        }

        if (!$persistenceLinkedLayout->shared) {
            throw new BadStateException('linkedZone', 'Linked zone is not in the shared layout.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedZone = $this->handler->updateZone(
                $persistenceZone,
                new ZoneUpdateStruct(
                    array(
                        'linkedZone' => $persistenceLinkedZone,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapZone($updatedZone);
    }

    /**
     * Removes the link in the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function unlinkZone(Zone $zone)
    {
        if ($zone->isPublished()) {
            throw new BadStateException('zone', 'Only draft zones can be unlinked.');
        }

        $persistenceZone = $this->handler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedZone = $this->handler->updateZone(
                $persistenceZone,
                new ZoneUpdateStruct(
                    array(
                        'linkedZone' => false,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapZone($updatedZone);
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function createLayout(APILayoutCreateStruct $layoutCreateStruct)
    {
        $this->validator->validateLayoutCreateStruct($layoutCreateStruct);

        if ($this->handler->layoutNameExists($layoutCreateStruct->name)) {
            throw new BadStateException('name', 'Layout with provided name already exists.');
        }

        $zoneCreateStructs = array();
        foreach ($layoutCreateStruct->layoutType->getZoneIdentifiers() as $zoneIdentifier) {
            $zoneCreateStructs[] = new ZoneCreateStruct(
                array(
                    'identifier' => $zoneIdentifier,
                )
            );
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdLayout = $this->handler->createLayout(
                new LayoutCreateStruct(
                    array(
                        'type' => $layoutCreateStruct->layoutType->getIdentifier(),
                        'name' => $layoutCreateStruct->name,
                        'status' => Value::STATUS_DRAFT,
                        'shared' => $layoutCreateStruct->shared,
                    )
                ),
                $zoneCreateStructs
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapLayout($createdLayout);
    }

    /**
     * Updates a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function updateLayout(Layout $layout, APILayoutUpdateStruct $layoutUpdateStruct)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Only draft layouts can be updated.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $this->validator->validateLayoutUpdateStruct($layoutUpdateStruct);

        if ($layoutUpdateStruct->name !== null) {
            if ($this->handler->layoutNameExists($layoutUpdateStruct->name, $persistenceLayout->id)) {
                throw new BadStateException('name', 'Layout with provided name already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedLayout = $this->handler->updateLayout(
                $persistenceLayout,
                new LayoutUpdateStruct(
                    array(
                        'name' => $layoutUpdateStruct->name,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapLayout($updatedLayout);
    }

    /**
     * Copies a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string $newName
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function copyLayout(Layout $layout, $newName)
    {
        $newName = is_string($newName) ? trim($newName) : $newName;
        $this->validator->validateLayoutName($newName, 'newName');

        if ($this->handler->layoutNameExists($newName, $layout->getId())) {
            throw new BadStateException('newName', 'Layout with provided name already exists.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), $layout->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedLayout = $this->handler->copyLayout($persistenceLayout, $newName);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapLayout($copiedLayout);
    }

    /**
     * Creates a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param bool $discardExisting
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not published
     *                                                          If draft already exists for layout and $discardExisting is set to false
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function createDraft(Layout $layout, $discardExisting = false)
    {
        if (!$layout->isPublished()) {
            throw new BadStateException('layout', 'Drafts can only be created from published layouts.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), Value::STATUS_PUBLISHED);

        if ($this->handler->layoutExists($persistenceLayout->id, Value::STATUS_DRAFT)) {
            if (!$discardExisting) {
                throw new BadStateException('layout', 'The provided layout already has a draft.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteLayout($persistenceLayout->id, Value::STATUS_DRAFT);
            $layoutDraft = $this->handler->createLayoutStatus($persistenceLayout, Value::STATUS_DRAFT);

            $layoutDraft = $this->handler->updateLayout(
                $layoutDraft,
                new LayoutUpdateStruct(
                    array(
                        'modified' => time(),
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapLayout($layoutDraft);
    }

    /**
     * Discards a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     */
    public function discardDraft(Layout $layout)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Only drafts can be discarded.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteLayout(
                $persistenceLayout->id,
                Value::STATUS_DRAFT
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
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function publishLayout(Layout $layout)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Only drafts can be published.');
        }

        $persistenceLayout = $this->handler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteLayout($persistenceLayout->id, Value::STATUS_ARCHIVED);

            if ($this->handler->layoutExists($persistenceLayout->id, Value::STATUS_PUBLISHED)) {
                $this->handler->createLayoutStatus(
                    $this->handler->loadLayout($persistenceLayout->id, Value::STATUS_PUBLISHED),
                    Value::STATUS_ARCHIVED
                );

                $this->handler->deleteLayout($persistenceLayout->id, Value::STATUS_PUBLISHED);
            }

            $publishedLayout = $this->handler->createLayoutStatus($persistenceLayout, Value::STATUS_PUBLISHED);
            $this->handler->deleteLayout($persistenceLayout->id, Value::STATUS_DRAFT);
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
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function deleteLayout(Layout $layout)
    {
        $persistenceLayout = $this->handler->loadLayout($layout->getId(), $layout->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteLayout(
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
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct
     */
    public function newLayoutCreateStruct(LayoutType $layoutType, $name)
    {
        return $this->structBuilder->newLayoutCreateStruct($layoutType, $name);
    }

    /**
     * Creates a new layout update struct.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct
     */
    public function newLayoutUpdateStruct()
    {
        return $this->structBuilder->newLayoutUpdateStruct();
    }
}
