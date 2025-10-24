<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Service;

use Netgen\Layouts\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutCopyStruct as APILayoutCopyStruct;
use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct as APILayoutCreateStruct;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use Netgen\Layouts\API\Values\Layout\LayoutUpdateStruct as APILayoutUpdateStruct;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Core\Mapper\LayoutMapper;
use Netgen\Layouts\Core\StructBuilder\LayoutStructBuilder;
use Netgen\Layouts\Core\Validator\LayoutValidator;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use Netgen\Layouts\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\Layouts\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\Layouts\Persistence\Values\Layout\ZoneUpdateStruct;
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
use Ramsey\Uuid\UuidInterface;

use function array_fill_keys;
use function array_map;
use function array_merge;
use function count;
use function sprintf;
use function trigger_deprecation;

final class LayoutService implements LayoutServiceInterface
{
    use TransactionTrait;

    private LayoutValidator $validator;

    private LayoutMapper $mapper;

    private LayoutStructBuilder $structBuilder;

    private LayoutHandlerInterface $layoutHandler;

    public function __construct(
        TransactionHandlerInterface $transactionHandler,
        LayoutValidator $validator,
        LayoutMapper $mapper,
        LayoutStructBuilder $structBuilder,
        LayoutHandlerInterface $layoutHandler
    ) {
        $this->transactionHandler = $transactionHandler;

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;
        $this->layoutHandler = $layoutHandler;
    }

    public function loadLayout(UuidInterface $layoutId): Layout
    {
        return $this->mapper->mapLayout(
            $this->layoutHandler->loadLayout(
                $layoutId,
                PersistenceStatus::Published,
            ),
        );
    }

    public function loadLayoutDraft(UuidInterface $layoutId): Layout
    {
        return $this->mapper->mapLayout(
            $this->layoutHandler->loadLayout(
                $layoutId,
                PersistenceStatus::Draft,
            ),
        );
    }

    public function loadLayoutArchive(UuidInterface $layoutId): Layout
    {
        return $this->mapper->mapLayout(
            $this->layoutHandler->loadLayout(
                $layoutId,
                PersistenceStatus::Archived,
            ),
        );
    }

    public function loadLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): LayoutList
    {
        $persistenceLayouts = $this->layoutHandler->loadLayouts(
            $includeDrafts,
            $offset,
            $limit,
        );

        return new LayoutList(
            array_map(
                fn (PersistenceLayout $layout): Layout => $this->mapper->mapLayout($layout),
                $persistenceLayouts,
            ),
        );
    }

    public function getLayoutsCount(bool $includeDrafts = false): int
    {
        return $this->layoutHandler->getLayoutsCount($includeDrafts);
    }

    public function loadSharedLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): LayoutList
    {
        $persistenceLayouts = $this->layoutHandler->loadSharedLayouts(
            $includeDrafts,
            $offset,
            $limit,
        );

        return new LayoutList(
            array_map(
                fn (PersistenceLayout $layout): Layout => $this->mapper->mapLayout($layout),
                $persistenceLayouts,
            ),
        );
    }

    public function getSharedLayoutsCount(bool $includeDrafts = false): int
    {
        return $this->layoutHandler->getSharedLayoutsCount($includeDrafts);
    }

    public function loadAllLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): LayoutList
    {
        $persistenceLayouts = $this->layoutHandler->loadAllLayouts(
            $includeDrafts,
            $offset,
            $limit,
        );

        return new LayoutList(
            array_map(
                fn (PersistenceLayout $layout): Layout => $this->mapper->mapLayout($layout),
                $persistenceLayouts,
            ),
        );
    }

    public function getAllLayoutsCount(bool $includeDrafts = false): int
    {
        return $this->layoutHandler->getAllLayoutsCount($includeDrafts);
    }

    public function loadRelatedLayouts(Layout $sharedLayout): LayoutList
    {
        if (!$sharedLayout->isPublished()) {
            throw new BadStateException('sharedLayout', 'Related layouts can only be loaded for published shared layouts.');
        }

        if (!$sharedLayout->isShared()) {
            throw new BadStateException('sharedLayout', 'Related layouts can only be loaded for shared layouts.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($sharedLayout->getId(), PersistenceStatus::from($sharedLayout->getStatus()->value));

        return new LayoutList(
            array_map(
                fn (PersistenceLayout $relatedLayout): Layout => $this->mapper->mapLayout($relatedLayout),
                $this->layoutHandler->loadRelatedLayouts($persistenceLayout),
            ),
        );
    }

    public function getRelatedLayoutsCount(Layout $sharedLayout): int
    {
        if (!$sharedLayout->isPublished()) {
            throw new BadStateException('sharedLayout', 'Count of related layouts can only be loaded for published shared layouts.');
        }

        if (!$sharedLayout->isShared()) {
            throw new BadStateException('sharedLayout', 'Count of related layouts can only be loaded for shared layouts.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($sharedLayout->getId(), PersistenceStatus::from($sharedLayout->getStatus()->value));

        return $this->layoutHandler->getRelatedLayoutsCount($persistenceLayout);
    }

    public function hasStatus(UuidInterface $layoutId, Status $status): bool
    {
        return $this->layoutHandler->layoutExists($layoutId, PersistenceStatus::from($status->value));
    }

    public function layoutExists(UuidInterface $layoutId, ?Status $status = null): bool
    {
        return $this->layoutHandler->layoutExists($layoutId, PersistenceStatus::tryFrom($status->value ?? -1));
    }

    public function layoutNameExists(string $name, ?UuidInterface $excludedLayoutId = null): bool
    {
        return $this->layoutHandler->layoutNameExists($name, $excludedLayoutId);
    }

    public function linkZone(Zone $zone, Zone $linkedZone): void
    {
        if (!$zone->isDraft()) {
            throw new BadStateException('zone', 'Only draft zones can be linked.');
        }

        if (!$linkedZone->isPublished()) {
            throw new BadStateException('linkedZone', 'Zones can only be linked to published zones.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($zone->getLayoutId(), PersistenceStatus::Draft);
        $persistenceZone = $this->layoutHandler->loadZone($zone->getLayoutId(), PersistenceStatus::Draft, $zone->getIdentifier());

        $persistenceLinkedLayout = $this->layoutHandler->loadLayout($linkedZone->getLayoutId(), PersistenceStatus::Published);
        $persistenceLinkedZone = $this->layoutHandler->loadZone($linkedZone->getLayoutId(), PersistenceStatus::Published, $linkedZone->getIdentifier());

        if ($persistenceLayout->shared) {
            throw new BadStateException('zone', 'Zone cannot be in the shared layout.');
        }

        if ($persistenceZone->layoutId === $persistenceLinkedZone->layoutId) {
            throw new BadStateException('linkedZone', 'Linked zone needs to be in a different layout.');
        }

        if (!$persistenceLinkedLayout->shared) {
            throw new BadStateException('linkedZone', 'Linked zone is not in the shared layout.');
        }

        $this->transaction(
            function () use ($persistenceZone, $persistenceLinkedZone): void {
                $this->layoutHandler->updateZone(
                    $persistenceZone,
                    ZoneUpdateStruct::fromArray(
                        [
                            'linkedZone' => $persistenceLinkedZone,
                        ],
                    ),
                );
            },
        );
    }

    public function unlinkZone(Zone $zone): void
    {
        if (!$zone->isDraft()) {
            throw new BadStateException('zone', 'Only draft zones can be unlinked.');
        }

        $persistenceZone = $this->layoutHandler->loadZone($zone->getLayoutId(), PersistenceStatus::Draft, $zone->getIdentifier());

        $this->transaction(
            function () use ($persistenceZone): void {
                $this->layoutHandler->updateZone(
                    $persistenceZone,
                    ZoneUpdateStruct::fromArray(
                        [
                            'linkedZone' => false,
                        ],
                    ),
                );
            },
        );
    }

    public function createLayout(APILayoutCreateStruct $layoutCreateStruct): Layout
    {
        $this->validator->validateLayoutCreateStruct($layoutCreateStruct);

        if ($this->layoutHandler->layoutNameExists($layoutCreateStruct->name)) {
            throw new BadStateException('name', 'Layout with provided name already exists.');
        }

        if ($layoutCreateStruct->description === null) {
            trigger_deprecation('netgen/layouts-core', '1.3', sprintf('Setting %s::$description property to null is deprecated. Since 2.0, only valid value will be a string.', APILayoutCreateStruct::class));
        }

        $createdLayout = $this->transaction(
            function () use ($layoutCreateStruct): PersistenceLayout {
                $createdLayout = $this->layoutHandler->createLayout(
                    LayoutCreateStruct::fromArray(
                        [
                            'uuid' => $layoutCreateStruct->uuid instanceof UuidInterface ?
                                $layoutCreateStruct->uuid->toString() :
                                $layoutCreateStruct->uuid,
                            'type' => $layoutCreateStruct->layoutType->getIdentifier(),
                            'name' => $layoutCreateStruct->name,
                            'description' => $layoutCreateStruct->description ?? '',
                            'status' => PersistenceStatus::Draft,
                            'shared' => $layoutCreateStruct->shared,
                            'mainLocale' => $layoutCreateStruct->mainLocale,
                        ],
                    ),
                );

                foreach ($layoutCreateStruct->layoutType->getZoneIdentifiers() as $zoneIdentifier) {
                    $this->layoutHandler->createZone(
                        $createdLayout,
                        ZoneCreateStruct::fromArray(
                            [
                                'identifier' => $zoneIdentifier,
                                'linkedZone' => null,
                            ],
                        ),
                    );
                }

                return $createdLayout;
            },
        );

        return $this->mapper->mapLayout($createdLayout);
    }

    public function addTranslation(Layout $layout, string $locale, string $sourceLocale): Layout
    {
        if (!$layout->isDraft()) {
            throw new BadStateException('layout', 'You can only add translation to draft layouts.');
        }

        $this->validator->validateLocale($locale, 'locale');
        $this->validator->validateLocale($sourceLocale, 'sourceLocale');

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Draft);

        $updatedLayout = $this->transaction(
            fn (): PersistenceLayout => $this->layoutHandler->createLayoutTranslation($persistenceLayout, $locale, $sourceLocale),
        );

        return $this->mapper->mapLayout($updatedLayout);
    }

    public function setMainTranslation(Layout $layout, string $mainLocale): Layout
    {
        if (!$layout->isDraft()) {
            throw new BadStateException('layout', 'You can only set main translation in draft layouts.');
        }

        $this->validator->validateLocale($mainLocale, 'mainLocale');

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Draft);

        $updatedLayout = $this->transaction(
            fn (): PersistenceLayout => $this->layoutHandler->setMainTranslation($persistenceLayout, $mainLocale),
        );

        return $this->mapper->mapLayout($updatedLayout);
    }

    public function removeTranslation(Layout $layout, string $locale): Layout
    {
        if (!$layout->isDraft()) {
            throw new BadStateException('layout', 'You can only remove translations from draft layouts.');
        }

        $this->validator->validateLocale($locale, 'locale');

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Draft);

        $updatedLayout = $this->transaction(
            fn (): PersistenceLayout => $this->layoutHandler->deleteLayoutTranslation($persistenceLayout, $locale),
        );

        return $this->mapper->mapLayout($updatedLayout);
    }

    public function updateLayout(Layout $layout, APILayoutUpdateStruct $layoutUpdateStruct): Layout
    {
        if (!$layout->isDraft()) {
            throw new BadStateException('layout', 'Only draft layouts can be updated.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Draft);

        $this->validator->validateLayoutUpdateStruct($layoutUpdateStruct);

        if ($layoutUpdateStruct->name !== null) {
            if ($this->layoutHandler->layoutNameExists($layoutUpdateStruct->name, $persistenceLayout->id)) {
                throw new BadStateException('name', 'Layout with provided name already exists.');
            }
        }

        $updatedLayout = $this->transaction(
            fn (): PersistenceLayout => $this->layoutHandler->updateLayout(
                $persistenceLayout,
                LayoutUpdateStruct::fromArray(
                    [
                        'name' => $layoutUpdateStruct->name,
                        'description' => $layoutUpdateStruct->description,
                    ],
                ),
            ),
        );

        return $this->mapper->mapLayout($updatedLayout);
    }

    public function copyLayout(Layout $layout, APILayoutCopyStruct $layoutCopyStruct): Layout
    {
        $this->validator->validateLayoutCopyStruct($layoutCopyStruct);

        if ($this->layoutHandler->layoutNameExists($layoutCopyStruct->name, $layout->getId())) {
            throw new BadStateException('layoutCopyStruct', 'Layout with provided name already exists.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::from($layout->getStatus()->value));

        $copiedLayout = $this->transaction(
            fn (): PersistenceLayout => $this->layoutHandler->copyLayout(
                $persistenceLayout,
                LayoutCopyStruct::fromArray(
                    [
                        'name' => $layoutCopyStruct->name,
                        'description' => $layoutCopyStruct->description,
                    ],
                ),
            ),
        );

        return $this->mapper->mapLayout($copiedLayout);
    }

    public function changeLayoutType(Layout $layout, LayoutTypeInterface $targetLayoutType, array $zoneMappings, bool $preserveSharedZones = true): Layout
    {
        if (!$layout->isDraft()) {
            throw new BadStateException('layout', 'Layout type can only be changed for draft layouts.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Draft);
        $layoutZones = $this->layoutHandler->loadLayoutZones($persistenceLayout);

        $this->validator->validateChangeLayoutType($layout, $targetLayoutType, $zoneMappings, $preserveSharedZones);

        $zoneMappings = array_merge(
            array_fill_keys($targetLayoutType->getZoneIdentifiers(), []),
            $zoneMappings,
        );

        $newLayout = $this->transaction(
            function () use ($persistenceLayout, $layoutZones, $targetLayoutType, $zoneMappings, $preserveSharedZones): PersistenceLayout {
                $updatedLayout = $this->layoutHandler->changeLayoutType(
                    $persistenceLayout,
                    $targetLayoutType->getIdentifier(),
                    $zoneMappings,
                );

                if (!$preserveSharedZones) {
                    return $updatedLayout;
                }

                foreach ($zoneMappings as $newZone => $oldZones) {
                    if (count($oldZones) !== 1) {
                        // Shared zones should always have 1:1 mapping with the new zone.
                        continue;
                    }

                    $oldZone = $layoutZones[$oldZones[0]];

                    if ($oldZone->linkedLayoutUuid !== null && $oldZone->linkedZoneIdentifier !== null) {
                        $this->layoutHandler->updateZone(
                            $this->layoutHandler->loadZone($updatedLayout->id, PersistenceStatus::Draft, $newZone),
                            ZoneUpdateStruct::fromArray(
                                [
                                    'linkedZone' => $this->layoutHandler->loadZone(
                                        $oldZone->linkedLayoutUuid,
                                        PersistenceStatus::Published,
                                        $oldZone->linkedZoneIdentifier,
                                    ),
                                ],
                            ),
                        );
                    }
                }

                return $updatedLayout;
            },
        );

        return $this->mapper->mapLayout($newLayout);
    }

    public function createDraft(Layout $layout, bool $discardExisting = false): Layout
    {
        if (!$layout->isPublished()) {
            throw new BadStateException('layout', 'Drafts can only be created from published layouts.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Published);

        if (!$discardExisting && $this->layoutHandler->layoutExists($persistenceLayout->id, PersistenceStatus::Draft)) {
            throw new BadStateException('layout', 'The provided layout already has a draft.');
        }

        $layoutDraft = $this->transaction(
            function () use ($persistenceLayout): PersistenceLayout {
                $this->layoutHandler->deleteLayout($persistenceLayout->id, PersistenceStatus::Draft);

                return $this->layoutHandler->createLayoutStatus($persistenceLayout, PersistenceStatus::Draft);
            },
        );

        return $this->mapper->mapLayout($layoutDraft);
    }

    public function discardDraft(Layout $layout): void
    {
        if (!$layout->isDraft()) {
            throw new BadStateException('layout', 'Only drafts can be discarded.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Draft);

        $this->transaction(
            function () use ($persistenceLayout): void {
                $this->layoutHandler->deleteLayout(
                    $persistenceLayout->id,
                    PersistenceStatus::Draft,
                );
            },
        );
    }

    public function publishLayout(Layout $layout): Layout
    {
        if (!$layout->isDraft()) {
            throw new BadStateException('layout', 'Only drafts can be published.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Draft);

        $publishedLayout = $this->transaction(
            function () use ($persistenceLayout): PersistenceLayout {
                $this->layoutHandler->deleteLayout($persistenceLayout->id, PersistenceStatus::Archived);

                if ($this->layoutHandler->layoutExists($persistenceLayout->id, PersistenceStatus::Published)) {
                    $currentlyPublishedLayout = $this->layoutHandler->loadLayout($persistenceLayout->id, PersistenceStatus::Published);
                    $archivedLayout = $this->layoutHandler->createLayoutStatus($currentlyPublishedLayout, PersistenceStatus::Archived);

                    // Update the archived layout to blank the name in order not to block
                    // usage of the old layout name. When restoring from archive, we need to
                    // reuse the name of the published layout.

                    // Also sets the modified date of the archived layout to the modified date of
                    // currently published layout, so we know when the archive was last published.
                    $this->layoutHandler->updateLayout(
                        $archivedLayout,
                        LayoutUpdateStruct::fromArray(
                            [
                                'name' => '',
                                'modified' => $currentlyPublishedLayout->modified,
                            ],
                        ),
                    );

                    $this->layoutHandler->deleteLayout($persistenceLayout->id, PersistenceStatus::Published);
                }

                $publishedLayout = $this->layoutHandler->createLayoutStatus($persistenceLayout, PersistenceStatus::Published);
                $this->layoutHandler->deleteLayout($persistenceLayout->id, PersistenceStatus::Draft);

                return $publishedLayout;
            },
        );

        return $this->mapper->mapLayout($publishedLayout);
    }

    public function restoreFromArchive(Layout $layout): Layout
    {
        if (!$layout->isArchived()) {
            throw new BadStateException('layout', 'Only archived layouts can be restored.');
        }

        $archivedLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Archived);
        $publishedLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Published);

        $draftLayout = null;

        try {
            $draftLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::Draft);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $draftLayout = $this->transaction(
            function () use ($draftLayout, $publishedLayout, $archivedLayout): PersistenceLayout {
                if ($draftLayout instanceof PersistenceLayout) {
                    $this->layoutHandler->deleteLayout($draftLayout->id, $draftLayout->status);
                }

                $draftLayout = $this->layoutHandler->createLayoutStatus($archivedLayout, PersistenceStatus::Draft);

                return $this->layoutHandler->updateLayout(
                    $draftLayout,
                    LayoutUpdateStruct::fromArray(
                        [
                            'name' => $publishedLayout->name,
                        ],
                    ),
                );
            },
        );

        return $this->mapper->mapLayout($draftLayout);
    }

    public function deleteLayout(Layout $layout): void
    {
        $persistenceLayout = $this->layoutHandler->loadLayout($layout->getId(), PersistenceStatus::from($layout->getStatus()->value));

        $this->transaction(
            function () use ($persistenceLayout): void {
                $this->layoutHandler->deleteLayout(
                    $persistenceLayout->id,
                );
            },
        );
    }

    public function newLayoutCreateStruct(LayoutTypeInterface $layoutType, string $name, string $mainLocale): APILayoutCreateStruct
    {
        return $this->structBuilder->newLayoutCreateStruct($layoutType, $name, $mainLocale);
    }

    public function newLayoutUpdateStruct(?Layout $layout = null): APILayoutUpdateStruct
    {
        return $this->structBuilder->newLayoutUpdateStruct($layout);
    }

    public function newLayoutCopyStruct(?Layout $layout = null): APILayoutCopyStruct
    {
        return $this->structBuilder->newLayoutCopyStruct($layout);
    }
}
