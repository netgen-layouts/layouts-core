<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Handler;

use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler;
use Netgen\Layouts\Persistence\Handler\BlockHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\Values\Block\BlockCreateStruct;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\Persistence\Values\Layout\Zone;
use Netgen\Layouts\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\Layouts\Persistence\Values\Layout\ZoneUpdateStruct;
use Netgen\Layouts\Persistence\Values\Value;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function array_values;
use function count;
use function in_array;
use function is_int;
use function is_string;
use function time;
use function trim;

final class LayoutHandler implements LayoutHandlerInterface
{
    private LayoutQueryHandler $queryHandler;

    private BlockHandlerInterface $blockHandler;

    private LayoutMapper $layoutMapper;

    public function __construct(
        LayoutQueryHandler $queryHandler,
        BlockHandlerInterface $blockHandler,
        LayoutMapper $layoutMapper
    ) {
        $this->queryHandler = $queryHandler;
        $this->blockHandler = $blockHandler;
        $this->layoutMapper = $layoutMapper;
    }

    public function loadLayout($layoutId, int $status): Layout
    {
        $layoutId = $layoutId instanceof UuidInterface ? $layoutId->toString() : $layoutId;
        $data = $this->queryHandler->loadLayoutData($layoutId, $status);

        if (count($data) === 0) {
            throw new NotFoundException('layout', $layoutId);
        }

        return $this->layoutMapper->mapLayouts($data)[0];
    }

    public function loadZone($layoutId, int $status, string $identifier): Zone
    {
        $layoutId = $layoutId instanceof UuidInterface ? $layoutId->toString() : $layoutId;
        $data = $this->queryHandler->loadZoneData($layoutId, $status, $identifier);

        if (count($data) === 0) {
            throw new NotFoundException('zone', $identifier);
        }

        return array_values($this->layoutMapper->mapZones($data))[0];
    }

    public function loadLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): array
    {
        $layoutIds = $this->queryHandler->loadLayoutIds($includeDrafts, false, $offset, $limit);
        $data = $this->queryHandler->loadLayoutsData($layoutIds, $includeDrafts);

        return $this->layoutMapper->mapLayouts($data);
    }

    public function getLayoutsCount(bool $includeDrafts = false): int
    {
        return $this->queryHandler->getLayoutsCount($includeDrafts, false);
    }

    public function loadSharedLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): array
    {
        $layoutIds = $this->queryHandler->loadLayoutIds($includeDrafts, true, $offset, $limit);
        $data = $this->queryHandler->loadLayoutsData($layoutIds, $includeDrafts);

        return $this->layoutMapper->mapLayouts($data);
    }

    public function getSharedLayoutsCount(bool $includeDrafts = false): int
    {
        return $this->queryHandler->getLayoutsCount($includeDrafts, true);
    }

    public function loadAllLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): array
    {
        $layoutIds = $this->queryHandler->loadLayoutIds($includeDrafts, null, $offset, $limit);
        $data = $this->queryHandler->loadLayoutsData($layoutIds, $includeDrafts);

        return $this->layoutMapper->mapLayouts($data);
    }

    public function getAllLayoutsCount(bool $includeDrafts = false): int
    {
        return $this->queryHandler->getLayoutsCount($includeDrafts);
    }

    public function loadRelatedLayouts(Layout $sharedLayout): array
    {
        $data = $this->queryHandler->loadRelatedLayoutsData($sharedLayout);

        return $this->layoutMapper->mapLayouts($data);
    }

    public function getRelatedLayoutsCount(Layout $sharedLayout): int
    {
        return $this->queryHandler->getRelatedLayoutsCount($sharedLayout);
    }

    public function layoutExists($layoutId, ?int $status = null): bool
    {
        $layoutId = $layoutId instanceof UuidInterface ? $layoutId->toString() : $layoutId;

        return $this->queryHandler->layoutExists($layoutId, $status);
    }

    public function loadLayoutZones(Layout $layout): array
    {
        return $this->layoutMapper->mapZones(
            $this->queryHandler->loadLayoutZonesData($layout),
        );
    }

    public function layoutNameExists(string $name, $excludedLayoutId = null): bool
    {
        $excludedLayoutId = $excludedLayoutId instanceof UuidInterface ?
            $excludedLayoutId->toString() :
            $excludedLayoutId;

        return $this->queryHandler->layoutNameExists($name, $excludedLayoutId);
    }

    public function createLayout(LayoutCreateStruct $layoutCreateStruct): Layout
    {
        if (is_string($layoutCreateStruct->uuid) && $this->layoutExists($layoutCreateStruct->uuid)) {
            throw new BadStateException('uuid', 'Layout with provided UUID already exists.');
        }

        $currentTimeStamp = time();

        $newLayout = Layout::fromArray(
            [
                'uuid' => is_string($layoutCreateStruct->uuid) ?
                    $layoutCreateStruct->uuid :
                    Uuid::uuid4()->toString(),
                'type' => $layoutCreateStruct->type,
                'name' => trim($layoutCreateStruct->name),
                'description' => trim($layoutCreateStruct->description),
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
                'status' => $layoutCreateStruct->status,
                'shared' => $layoutCreateStruct->shared ? true : false,
                'mainLocale' => $layoutCreateStruct->mainLocale,
                'availableLocales' => [$layoutCreateStruct->mainLocale],
            ],
        );

        $newLayout = $this->queryHandler->createLayout($newLayout);

        $this->queryHandler->createLayoutTranslation(
            $newLayout,
            $layoutCreateStruct->mainLocale,
        );

        return $newLayout;
    }

    public function createLayoutTranslation(Layout $layout, string $locale, string $sourceLocale): Layout
    {
        if (in_array($locale, $layout->availableLocales, true)) {
            throw new BadStateException('locale', 'Layout already has the provided locale.');
        }

        if (!in_array($sourceLocale, $layout->availableLocales, true)) {
            throw new BadStateException('sourceLocale', 'Layout does not have the provided source locale.');
        }

        $updatedLayout = clone $layout;
        $updatedLayout->availableLocales[] = $locale;
        $updatedLayout->modified = time();

        $this->queryHandler->createLayoutTranslation($layout, $locale);

        foreach ($this->blockHandler->loadLayoutBlocks($updatedLayout) as $block) {
            if ($block->isTranslatable) {
                $this->blockHandler->createBlockTranslation($block, $locale, $sourceLocale);
            }
        }

        return $updatedLayout;
    }

    public function setMainTranslation(Layout $layout, string $mainLocale): Layout
    {
        if (!in_array($mainLocale, $layout->availableLocales, true)) {
            throw new BadStateException('mainLocale', 'Layout does not have the provided locale.');
        }

        $updatedLayout = clone $layout;
        $updatedLayout->mainLocale = $mainLocale;
        $updatedLayout->modified = time();

        $this->queryHandler->updateLayout($updatedLayout);

        foreach ($this->blockHandler->loadLayoutBlocks($updatedLayout) as $block) {
            $oldMainLocale = $block->mainLocale;
            if (!$block->isTranslatable && $oldMainLocale !== $mainLocale) {
                $block = $this->blockHandler->createBlockTranslation($block, $mainLocale, $oldMainLocale);
            }

            $block = $this->blockHandler->setMainTranslation($block, $mainLocale);

            if (!$block->isTranslatable && $oldMainLocale !== $mainLocale) {
                $this->blockHandler->deleteBlockTranslation($block, $oldMainLocale);
            }
        }

        return $updatedLayout;
    }

    public function createZone(Layout $layout, ZoneCreateStruct $zoneCreateStruct): Zone
    {
        $rootBlock = $this->blockHandler->createBlock(
            BlockCreateStruct::fromArray(
                [
                    'status' => $layout->status,
                    'position' => null,
                    'definitionIdentifier' => '',
                    'viewType' => '',
                    'itemViewType' => '',
                    'name' => '',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'parameters' => [],
                    'config' => [],
                ],
            ),
            $layout,
        );

        $newZoneData = [
            'layoutId' => $layout->id,
            'layoutUuid' => $layout->uuid,
            'status' => $layout->status,
            'rootBlockId' => $rootBlock->id,
            'identifier' => $zoneCreateStruct->identifier,
            'linkedLayoutUuid' => null,
            'linkedZoneIdentifier' => null,
        ];

        if ($zoneCreateStruct->linkedZone instanceof Zone) {
            $newZoneData['linkedLayoutUuid'] = $zoneCreateStruct->linkedZone->layoutUuid;
            $newZoneData['linkedZoneIdentifier'] = $zoneCreateStruct->linkedZone->identifier;
        }

        $newZone = Zone::fromArray($newZoneData);
        $this->queryHandler->createZone($newZone);

        return $newZone;
    }

    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct): Layout
    {
        $updatedLayout = clone $layout;
        $updatedLayout->modified = time();

        if (is_string($layoutUpdateStruct->name)) {
            $updatedLayout->name = trim($layoutUpdateStruct->name);
        }

        if (is_int($layoutUpdateStruct->modified)) {
            $updatedLayout->modified = $layoutUpdateStruct->modified;
        }

        if (is_string($layoutUpdateStruct->description)) {
            $updatedLayout->description = trim($layoutUpdateStruct->description);
        }

        $this->queryHandler->updateLayout($updatedLayout);

        return $updatedLayout;
    }

    public function updateZone(Zone $zone, ZoneUpdateStruct $zoneUpdateStruct): Zone
    {
        $updatedZone = clone $zone;

        if ($zoneUpdateStruct->linkedZone instanceof Zone) {
            $updatedZone->linkedLayoutUuid = $zoneUpdateStruct->linkedZone->layoutUuid;
            $updatedZone->linkedZoneIdentifier = $zoneUpdateStruct->linkedZone->identifier;
        } elseif ($zoneUpdateStruct->linkedZone !== null) {
            // Linked zone other than a zone object (e.g. false) indicates we want to remove the link
            $updatedZone->linkedLayoutUuid = null;
            $updatedZone->linkedZoneIdentifier = null;
        }

        $this->queryHandler->updateZone($updatedZone);

        return $updatedZone;
    }

    public function copyLayout(Layout $layout, LayoutCopyStruct $layoutCopyStruct): Layout
    {
        $copiedLayout = clone $layout;

        unset($copiedLayout->id);
        $copiedLayout->uuid = Uuid::uuid4()->toString();

        $currentTimeStamp = time();
        $copiedLayout->created = $currentTimeStamp;
        $copiedLayout->modified = $currentTimeStamp;
        $copiedLayout->name = trim($layoutCopyStruct->name);

        if (is_string($layoutCopyStruct->description)) {
            $copiedLayout->description = trim($layoutCopyStruct->description);
        }

        $copiedLayout = $this->queryHandler->createLayout($copiedLayout);

        foreach ($copiedLayout->availableLocales as $locale) {
            $this->queryHandler->createLayoutTranslation($copiedLayout, $locale);
        }

        $layoutZones = $this->loadLayoutZones($layout);
        foreach ($layoutZones as $layoutZone) {
            $linkedZone = null;

            if ($layoutZone->linkedLayoutUuid !== null && $layoutZone->linkedZoneIdentifier !== null) {
                try {
                    $linkedZone = $this->loadZone(
                        $layoutZone->linkedLayoutUuid,
                        Value::STATUS_PUBLISHED,
                        $layoutZone->linkedZoneIdentifier,
                    );
                } catch (NotFoundException $e) {
                    // Do nothing
                }
            }

            $zoneCreateStruct = ZoneCreateStruct::fromArray(
                [
                    'identifier' => $layoutZone->identifier,
                    'linkedZone' => $linkedZone,
                ],
            );

            $createdZone = $this->createZone($copiedLayout, $zoneCreateStruct);
            $rootBlock = $this->blockHandler->loadBlock(
                $createdZone->rootBlockId,
                $createdZone->status,
            );

            $zoneBlocks = $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock($layoutZone->rootBlockId, $layoutZone->status),
            );

            foreach ($zoneBlocks as $block) {
                $this->blockHandler->copyBlock($block, $rootBlock, 'root');
            }
        }

        return $copiedLayout;
    }

    public function changeLayoutType(Layout $layout, string $targetLayoutType, array $zoneMappings): Layout
    {
        $newRootBlocks = [];
        $oldRootBlocks = [];
        $oldZones = $this->loadLayoutZones($layout);

        foreach ($oldZones as $zoneIdentifier => $oldZone) {
            $oldRootBlocks[$zoneIdentifier] = $this->blockHandler->loadBlock(
                $oldZone->rootBlockId,
                $oldZone->status,
            );
        }

        foreach ($zoneMappings as $newZoneIdentifier => $mappedZones) {
            $newRootBlocks[$newZoneIdentifier] = $this->blockHandler->createBlock(
                BlockCreateStruct::fromArray(
                    [
                        'status' => $layout->status,
                        'position' => null,
                        'definitionIdentifier' => '',
                        'viewType' => '',
                        'itemViewType' => '',
                        'name' => '',
                        'isTranslatable' => false,
                        'alwaysAvailable' => true,
                        'parameters' => [],
                        'config' => [],
                    ],
                ),
                $layout,
            );

            $i = 0;
            foreach ($mappedZones as $mappedZone) {
                $blocks = $this->blockHandler->loadChildBlocks($oldRootBlocks[$mappedZone]);
                foreach ($blocks as $block) {
                    $this->blockHandler->moveBlock($block, $newRootBlocks[$newZoneIdentifier], 'root', $i);
                    ++$i;
                }
            }
        }

        foreach ($oldZones as $oldZone) {
            $this->queryHandler->deleteZone($oldZone->layoutId, $oldZone->identifier, $oldZone->status);
            $this->blockHandler->deleteBlock($oldRootBlocks[$oldZone->identifier]);
        }

        foreach ($newRootBlocks as $newZoneIdentifier => $rootBlock) {
            $newZone = Zone::fromArray(
                [
                    'layoutId' => $layout->id,
                    'layoutUuid' => $layout->uuid,
                    'status' => $layout->status,
                    'rootBlockId' => $rootBlock->id,
                    'identifier' => $newZoneIdentifier,
                    'linkedLayoutUuid' => null,
                    'linkedZoneIdentifier' => null,
                ],
            );

            $this->queryHandler->createZone($newZone);
        }

        $newLayout = clone $layout;
        $newLayout->type = $targetLayoutType;
        $newLayout->modified = time();

        $this->queryHandler->updateLayout($newLayout);

        return $newLayout;
    }

    public function createLayoutStatus(Layout $layout, int $newStatus): Layout
    {
        $newLayout = clone $layout;
        $newLayout->status = $newStatus;
        $newLayout->modified = time();

        $this->queryHandler->createLayout($newLayout);
        foreach ($newLayout->availableLocales as $locale) {
            $this->queryHandler->createLayoutTranslation($newLayout, $locale);
        }

        $layoutBlocks = $this->blockHandler->loadLayoutBlocks($layout);
        foreach ($layoutBlocks as $block) {
            $this->blockHandler->createBlockStatus($block, $newStatus);
        }

        $layoutZones = $this->loadLayoutZones($layout);
        foreach ($layoutZones as $layoutZone) {
            $newZone = clone $layoutZone;
            $newZone->status = $newStatus;

            $this->queryHandler->createZone($newZone);
        }

        return $newLayout;
    }

    public function deleteLayout(int $layoutId, ?int $status = null): void
    {
        $this->queryHandler->deleteLayoutZones($layoutId, $status);
        $this->blockHandler->deleteLayoutBlocks($layoutId, $status);
        $this->queryHandler->deleteLayoutTranslations($layoutId, $status);
        $this->queryHandler->deleteLayout($layoutId, $status);
    }

    public function deleteLayoutTranslation(Layout $layout, string $locale): Layout
    {
        if (!in_array($locale, $layout->availableLocales, true)) {
            throw new BadStateException('locale', 'Layout does not have the provided locale.');
        }

        if ($locale === $layout->mainLocale) {
            throw new BadStateException('locale', 'Main translation cannot be removed from the layout.');
        }

        $this->updateLayoutModifiedDate($layout);

        $this->queryHandler->deleteLayoutTranslations($layout->id, $layout->status, $locale);

        foreach ($this->blockHandler->loadLayoutBlocks($layout) as $block) {
            if (!in_array($locale, $block->availableLocales, true)) {
                continue;
            }

            if (count($block->availableLocales) > 1) {
                $this->blockHandler->deleteBlockTranslation($block, $locale);
            } elseif ($block->parentId !== null) {
                // This case should never happen (when block has only one translation,
                // which is not the main one), but if it does, we will delete the block
                // to preserve the data integrity.
                $this->blockHandler->deleteBlock($block);
            }
        }

        return $this->loadLayout($layout->id, $layout->status);
    }

    /**
     * Updates the layout modified date to the current timestamp.
     */
    private function updateLayoutModifiedDate(Layout $layout): void
    {
        $updatedLayout = clone $layout;
        $updatedLayout->modified = time();
        $this->queryHandler->updateLayout($updatedLayout);
    }
}
