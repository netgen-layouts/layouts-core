<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct as APILayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct as APILayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct as APILayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Persistence\HandlerInterface;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct;

final class LayoutService extends Service implements LayoutServiceInterface
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\LayoutValidator
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    private $mapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder
     */
    private $structBuilder;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface
     */
    private $blockHandler;

    public function __construct(
        HandlerInterface $persistenceHandler,
        LayoutValidator $validator,
        LayoutMapper $mapper,
        LayoutStructBuilder $structBuilder
    ) {
        parent::__construct($persistenceHandler);

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;

        $this->layoutHandler = $persistenceHandler->getLayoutHandler();
        $this->blockHandler = $persistenceHandler->getBlockHandler();
    }

    public function loadLayout($layoutId)
    {
        $this->validator->validateId($layoutId, 'layoutId');

        return $this->mapper->mapLayout(
            $this->layoutHandler->loadLayout(
                $layoutId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    public function loadLayoutDraft($layoutId)
    {
        $this->validator->validateId($layoutId, 'layoutId');

        return $this->mapper->mapLayout(
            $this->layoutHandler->loadLayout(
                $layoutId,
                Value::STATUS_DRAFT
            )
        );
    }

    public function loadLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $this->validator->validateOffsetAndLimit($offset, $limit);

        $persistenceLayouts = $this->layoutHandler->loadLayouts(
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

    public function loadSharedLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $this->validator->validateOffsetAndLimit($offset, $limit);

        $persistenceLayouts = $this->layoutHandler->loadSharedLayouts(
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

    public function loadRelatedLayouts(Layout $sharedLayout, $offset = 0, $limit = null)
    {
        if (!$sharedLayout->isPublished()) {
            throw new BadStateException('sharedLayout', 'Related layouts can only be loaded for published shared layouts.');
        }

        if (!$sharedLayout->isShared()) {
            throw new BadStateException('sharedLayout', 'Related layouts can only be loaded for shared layouts.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($sharedLayout->getId(), $sharedLayout->getStatus());

        $relatedPersistenceLayouts = $this->layoutHandler->loadRelatedLayouts(
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

    public function getRelatedLayoutsCount(Layout $sharedLayout)
    {
        if (!$sharedLayout->isPublished()) {
            throw new BadStateException('sharedLayout', 'Count of related layouts can only be loaded for published shared layouts.');
        }

        if (!$sharedLayout->isShared()) {
            throw new BadStateException('sharedLayout', 'Count of related layouts can only be loaded for shared layouts.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($sharedLayout->getId(), $sharedLayout->getStatus());

        return $this->layoutHandler->getRelatedLayoutsCount($persistenceLayout);
    }

    public function hasPublishedState(Layout $layout)
    {
        return $this->layoutHandler->layoutExists($layout->getId(), Value::STATUS_PUBLISHED);
    }

    public function loadZone($layoutId, $identifier)
    {
        $this->validator->validateId($layoutId, 'layoutId');
        $this->validator->validateIdentifier($identifier, 'identifier', true);

        return $this->mapper->mapZone(
            $this->layoutHandler->loadZone(
                $layoutId,
                Value::STATUS_PUBLISHED,
                $identifier
            )
        );
    }

    public function loadZoneDraft($layoutId, $identifier)
    {
        $this->validator->validateId($layoutId, 'layoutId');
        $this->validator->validateIdentifier($identifier, 'identifier', true);

        return $this->mapper->mapZone(
            $this->layoutHandler->loadZone(
                $layoutId,
                Value::STATUS_DRAFT,
                $identifier
            )
        );
    }

    public function layoutNameExists($name, $excludedLayoutId = null)
    {
        return $this->layoutHandler->layoutNameExists($name, $excludedLayoutId);
    }

    public function linkZone(Zone $zone, Zone $linkedZone)
    {
        if ($zone->isPublished()) {
            throw new BadStateException('zone', 'Only draft zones can be linked.');
        }

        if (!$linkedZone->isPublished()) {
            throw new BadStateException('linkedZone', 'Zones can only be linked to published zones.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($zone->getLayoutId(), Value::STATUS_DRAFT);
        $persistenceZone = $this->layoutHandler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());

        $persistenceLinkedLayout = $this->layoutHandler->loadLayout($linkedZone->getLayoutId(), Value::STATUS_PUBLISHED);
        $persistenceLinkedZone = $this->layoutHandler->loadZone($linkedZone->getLayoutId(), Value::STATUS_PUBLISHED, $linkedZone->getIdentifier());

        if ($persistenceLayout->shared) {
            throw new BadStateException('zone', 'Zone cannot be in the shared layout.');
        }

        if ($persistenceZone->layoutId === $persistenceLinkedZone->layoutId) {
            throw new BadStateException('linkedZone', 'Linked zone needs to be in a different layout.');
        }

        if (!$persistenceLinkedLayout->shared) {
            throw new BadStateException('linkedZone', 'Linked zone is not in the shared layout.');
        }

        $updatedZone = $this->transaction(
            function () use ($persistenceZone, $persistenceLinkedZone) {
                return $this->layoutHandler->updateZone(
                    $persistenceZone,
                    new ZoneUpdateStruct(
                        array(
                            'linkedZone' => $persistenceLinkedZone,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapZone($updatedZone);
    }

    public function unlinkZone(Zone $zone)
    {
        if ($zone->isPublished()) {
            throw new BadStateException('zone', 'Only draft zones can be unlinked.');
        }

        $persistenceZone = $this->layoutHandler->loadZone($zone->getLayoutId(), Value::STATUS_DRAFT, $zone->getIdentifier());

        $updatedZone = $this->transaction(
            function () use ($persistenceZone) {
                return $this->layoutHandler->updateZone(
                    $persistenceZone,
                    new ZoneUpdateStruct(
                        array(
                            'linkedZone' => false,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapZone($updatedZone);
    }

    public function createLayout(APILayoutCreateStruct $layoutCreateStruct)
    {
        $this->validator->validateLayoutCreateStruct($layoutCreateStruct);

        if ($this->layoutHandler->layoutNameExists($layoutCreateStruct->name)) {
            throw new BadStateException('name', 'Layout with provided name already exists.');
        }

        $createdLayout = $this->transaction(
            function () use ($layoutCreateStruct) {
                $createdLayout = $this->layoutHandler->createLayout(
                    new LayoutCreateStruct(
                        array(
                            'type' => $layoutCreateStruct->layoutType->getIdentifier(),
                            'name' => $layoutCreateStruct->name,
                            'description' => $layoutCreateStruct->description,
                            'status' => Value::STATUS_DRAFT,
                            'shared' => $layoutCreateStruct->shared,
                            'mainLocale' => $layoutCreateStruct->mainLocale,
                        )
                    )
                );

                foreach ($layoutCreateStruct->layoutType->getZoneIdentifiers() as $zoneIdentifier) {
                    $this->layoutHandler->createZone(
                        $createdLayout,
                        new ZoneCreateStruct(
                            array(
                                'identifier' => $zoneIdentifier,
                            )
                        )
                    );
                }

                return $createdLayout;
            }
        );

        return $this->mapper->mapLayout($createdLayout);
    }

    public function addTranslation(Layout $layout, $locale, $sourceLocale)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'You can only add translation to draft layouts.');
        }

        $this->validator->validateLocale($locale, 'locale');
        $this->validator->validateLocale($sourceLocale, 'sourceLocale');

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $updatedLayout = $this->transaction(
            function () use ($persistenceLayout, $locale, $sourceLocale) {
                return $this->layoutHandler->createLayoutTranslation($persistenceLayout, $locale, $sourceLocale);
            }
        );

        return $this->mapper->mapLayout($updatedLayout);
    }

    public function setMainTranslation(Layout $layout, $mainLocale)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'You can only set main translation in draft layouts.');
        }

        $this->validator->validateLocale($mainLocale, 'mainLocale');

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $updatedLayout = $this->transaction(
            function () use ($persistenceLayout, $mainLocale) {
                return $this->layoutHandler->setMainTranslation($persistenceLayout, $mainLocale);
            }
        );

        return $this->mapper->mapLayout($updatedLayout);
    }

    public function removeTranslation(Layout $layout, $locale)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'You can only remove translations from draft layouts.');
        }

        $this->validator->validateLocale($locale, 'locale');

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $updatedLayout = $this->transaction(
            function () use ($persistenceLayout, $locale) {
                return $this->layoutHandler->deleteLayoutTranslation($persistenceLayout, $locale);
            }
        );

        return $this->mapper->mapLayout($updatedLayout);
    }

    public function updateLayout(Layout $layout, APILayoutUpdateStruct $layoutUpdateStruct)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Only draft layouts can be updated.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $this->validator->validateLayoutUpdateStruct($layoutUpdateStruct);

        if ($layoutUpdateStruct->name !== null) {
            if ($this->layoutHandler->layoutNameExists($layoutUpdateStruct->name, $persistenceLayout->id)) {
                throw new BadStateException('name', 'Layout with provided name already exists.');
            }
        }

        $updatedLayout = $this->transaction(
            function () use ($persistenceLayout, $layoutUpdateStruct) {
                return $this->layoutHandler->updateLayout(
                    $persistenceLayout,
                    new LayoutUpdateStruct(
                        array(
                            'name' => $layoutUpdateStruct->name,
                            'description' => $layoutUpdateStruct->description,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapLayout($updatedLayout);
    }

    public function copyLayout(Layout $layout, APILayoutCopyStruct $layoutCopyStruct)
    {
        $this->validator->validateLayoutCopyStruct($layoutCopyStruct);

        if ($this->layoutHandler->layoutNameExists($layoutCopyStruct->name, $layout->getId())) {
            throw new BadStateException('layoutCopyStruct', 'Layout with provided name already exists.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), $layout->getStatus());

        $copiedLayout = $this->transaction(
            function () use ($persistenceLayout, $layoutCopyStruct) {
                return $this->layoutHandler->copyLayout(
                    $persistenceLayout,
                    new LayoutCopyStruct(
                        array(
                            'name' => $layoutCopyStruct->name,
                            'description' => $layoutCopyStruct->description,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapLayout($copiedLayout);
    }

    public function changeLayoutType(Layout $layout, LayoutType $targetLayoutType, array $zoneMappings = array(), $preserveSharedZones = true)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Layout type can only be changed for draft layouts.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        if ($persistenceLayout->type === $targetLayoutType->getIdentifier()) {
            throw new BadStateException('layout', 'Layout is already of provided target type.');
        }

        $this->validator->validateChangeLayoutType($layout, $targetLayoutType, $zoneMappings, $preserveSharedZones);

        $zoneMappings = array_merge(
            array_fill_keys($targetLayoutType->getZoneIdentifiers(), array()),
            $zoneMappings
        );

        $newLayout = $this->transaction(
            function () use ($layout, $persistenceLayout, $targetLayoutType, $zoneMappings, $preserveSharedZones) {
                $updatedLayout = $this->layoutHandler->changeLayoutType(
                    $persistenceLayout,
                    $targetLayoutType->getIdentifier(),
                    $zoneMappings
                );

                if ($preserveSharedZones) {
                    foreach ($zoneMappings as $newZone => $oldZones) {
                        if (count($oldZones) === 1 && $layout->getZone($oldZones[0], true)->hasLinkedZone()) {
                            $this->linkZone(
                                $this->loadZoneDraft($updatedLayout->id, $newZone),
                                $layout->getZone($oldZones[0], true)->getLinkedZone()
                            );
                        }
                    }
                }

                return $updatedLayout;
            }
        );

        return $this->mapper->mapLayout($newLayout);
    }

    public function createDraft(Layout $layout, $discardExisting = false)
    {
        if (!$layout->isPublished()) {
            throw new BadStateException('layout', 'Drafts can only be created from published layouts.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_PUBLISHED);

        if ($this->layoutHandler->layoutExists($persistenceLayout->id, Value::STATUS_DRAFT)) {
            if (!$discardExisting) {
                throw new BadStateException('layout', 'The provided layout already has a draft.');
            }
        }

        $layoutDraft = $this->transaction(
            function () use ($persistenceLayout) {
                $this->layoutHandler->deleteLayout($persistenceLayout->id, Value::STATUS_DRAFT);

                return $this->layoutHandler->createLayoutStatus($persistenceLayout, Value::STATUS_DRAFT);
            }
        );

        return $this->mapper->mapLayout($layoutDraft);
    }

    public function discardDraft(Layout $layout)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Only drafts can be discarded.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceLayout) {
                $this->layoutHandler->deleteLayout(
                    $persistenceLayout->id,
                    Value::STATUS_DRAFT
                );
            }
        );
    }

    public function publishLayout(Layout $layout)
    {
        if ($layout->isPublished()) {
            throw new BadStateException('layout', 'Only drafts can be published.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), Value::STATUS_DRAFT);

        $publishedLayout = $this->transaction(
            function () use ($persistenceLayout) {
                $this->layoutHandler->deleteLayout($persistenceLayout->id, Value::STATUS_ARCHIVED);

                if ($this->layoutHandler->layoutExists($persistenceLayout->id, Value::STATUS_PUBLISHED)) {
                    $archivedLayout = $this->layoutHandler->createLayoutStatus(
                        $this->layoutHandler->loadLayout($persistenceLayout->id, Value::STATUS_PUBLISHED),
                        Value::STATUS_ARCHIVED
                    );

                    // Update the archived layout to blank the name in order not to block
                    // usage of the old layout name.
                    // When restoring from archive, we need to reuse the name of the published
                    // layout.
                    $this->layoutHandler->updateLayout($archivedLayout, new LayoutUpdateStruct(array('name' => '')));

                    $this->layoutHandler->deleteLayout($persistenceLayout->id, Value::STATUS_PUBLISHED);
                }

                $publishedLayout = $this->layoutHandler->createLayoutStatus($persistenceLayout, Value::STATUS_PUBLISHED);
                $this->layoutHandler->deleteLayout($persistenceLayout->id, Value::STATUS_DRAFT);

                return $publishedLayout;
            }
        );

        return $this->mapper->mapLayout($publishedLayout);
    }

    public function restoreFromArchive($layoutId)
    {
        $archivedLayout = $this->layoutHandler->loadLayout($layoutId, Value::STATUS_ARCHIVED);
        $publishedLayout = $this->layoutHandler->loadLayout($layoutId, Value::STATUS_PUBLISHED);

        $draftLayout = null;
        try {
            $draftLayout = $this->layoutHandler->loadLayout($layoutId, Value::STATUS_DRAFT);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $draftLayout = $this->transaction(
            function () use ($draftLayout, $publishedLayout, $archivedLayout) {
                if ($draftLayout instanceof PersistenceLayout) {
                    $this->layoutHandler->deleteLayout($draftLayout->id, $draftLayout->status);
                }

                $draftLayout = $this->layoutHandler->createLayoutStatus($archivedLayout, Value::STATUS_DRAFT);

                return $this->layoutHandler->updateLayout(
                    $draftLayout,
                    new LayoutUpdateStruct(
                        array(
                            'name' => $publishedLayout->name,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapLayout($draftLayout);
    }

    public function deleteLayout(Layout $layout)
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), $layout->getStatus());

        $this->transaction(
            function () use ($persistenceLayout) {
                $this->layoutHandler->deleteLayout(
                    $persistenceLayout->id
                );
            }
        );
    }

    public function newLayoutCreateStruct(LayoutType $layoutType, $name, $mainLocale)
    {
        return $this->structBuilder->newLayoutCreateStruct($layoutType, $name, $mainLocale);
    }

    public function newLayoutUpdateStruct(Layout $layout = null)
    {
        return $this->structBuilder->newLayoutUpdateStruct($layout);
    }

    public function newLayoutCopyStruct(Layout $layout = null)
    {
        return $this->structBuilder->newLayoutCopyStruct($layout);
    }
}
