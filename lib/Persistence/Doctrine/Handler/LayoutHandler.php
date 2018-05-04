<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler;
use Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct;

final class LayoutHandler implements LayoutHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler
     */
    private $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface
     */
    private $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper
     */
    private $layoutMapper;

    public function __construct(
        LayoutQueryHandler $queryHandler,
        BlockHandlerInterface $blockHandler,
        LayoutMapper $layoutMapper
    ) {
        $this->queryHandler = $queryHandler;
        $this->blockHandler = $blockHandler;
        $this->layoutMapper = $layoutMapper;
    }

    public function loadLayout($layoutId, $status)
    {
        $data = $this->queryHandler->loadLayoutData($layoutId, $status);

        if (empty($data)) {
            throw new NotFoundException('layout', $layoutId);
        }

        $data = $this->layoutMapper->mapLayouts($data);

        return reset($data);
    }

    public function loadZone($layoutId, $status, $identifier)
    {
        $data = $this->queryHandler->loadZoneData($layoutId, $status, $identifier);

        if (empty($data)) {
            throw new NotFoundException('zone', $identifier);
        }

        $data = $this->layoutMapper->mapZones($data);

        return reset($data);
    }

    public function loadLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $data = $this->queryHandler->loadLayoutsData($includeDrafts, false, $offset, $limit);

        return $this->layoutMapper->mapLayouts($data);
    }

    public function loadSharedLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $data = $this->queryHandler->loadLayoutsData($includeDrafts, true, $offset, $limit);

        return $this->layoutMapper->mapLayouts($data);
    }

    public function loadRelatedLayouts(Layout $sharedLayout, $offset = 0, $limit = null)
    {
        $data = $this->queryHandler->loadRelatedLayoutsData($sharedLayout, $offset, $limit);

        return $this->layoutMapper->mapLayouts($data);
    }

    public function getRelatedLayoutsCount(Layout $sharedLayout)
    {
        return $this->queryHandler->getRelatedLayoutsCount($sharedLayout);
    }

    public function layoutExists($layoutId, $status)
    {
        return $this->queryHandler->layoutExists($layoutId, $status);
    }

    public function zoneExists($layoutId, $status, $identifier)
    {
        return $this->queryHandler->zoneExists($layoutId, $status, $identifier);
    }

    public function loadLayoutZones(Layout $layout)
    {
        return $this->layoutMapper->mapZones(
            $this->queryHandler->loadLayoutZonesData($layout)
        );
    }

    public function layoutNameExists($name, $excludedLayoutId = null)
    {
        return $this->queryHandler->layoutNameExists($name, $excludedLayoutId);
    }

    public function createLayout(LayoutCreateStruct $layoutCreateStruct)
    {
        $currentTimeStamp = time();

        $newLayout = new Layout(
            [
                'type' => $layoutCreateStruct->type,
                'name' => trim($layoutCreateStruct->name),
                'description' => trim($layoutCreateStruct->description),
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
                'status' => $layoutCreateStruct->status,
                'shared' => $layoutCreateStruct->shared ? true : false,
                'mainLocale' => $layoutCreateStruct->mainLocale,
                'availableLocales' => [$layoutCreateStruct->mainLocale],
            ]
        );

        $newLayout = $this->queryHandler->createLayout($newLayout);

        $this->queryHandler->createLayoutTranslation(
            $newLayout,
            $layoutCreateStruct->mainLocale
        );

        return $newLayout;
    }

    public function createLayoutTranslation(Layout $layout, $locale, $sourceLocale)
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

    public function setMainTranslation(Layout $layout, $mainLocale)
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

    public function createZone(Layout $layout, ZoneCreateStruct $zoneCreateStruct)
    {
        $rootBlock = $this->blockHandler->createBlock(
            new BlockCreateStruct(
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
                ]
            ),
            $layout
        );

        $newZone = new Zone(
            [
                'layoutId' => $layout->id,
                'status' => $layout->status,
                'rootBlockId' => $rootBlock->id,
                'identifier' => $zoneCreateStruct->identifier,
                'linkedLayoutId' => $zoneCreateStruct->linkedLayoutId,
                'linkedZoneIdentifier' => $zoneCreateStruct->linkedZoneIdentifier,
            ]
        );

        $this->queryHandler->createZone($newZone);

        return $newZone;
    }

    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct)
    {
        $updatedLayout = clone $layout;
        $updatedLayout->modified = time();

        if ($layoutUpdateStruct->name !== null) {
            $updatedLayout->name = trim($layoutUpdateStruct->name);
        }

        if ($layoutUpdateStruct->modified !== null) {
            $updatedLayout->modified = (int) $layoutUpdateStruct->modified;
        }

        if ($layoutUpdateStruct->description !== null) {
            $updatedLayout->description = trim($layoutUpdateStruct->description);
        }

        $this->queryHandler->updateLayout($updatedLayout);

        return $updatedLayout;
    }

    public function updateZone(Zone $zone, ZoneUpdateStruct $zoneUpdateStruct)
    {
        $updatedZone = clone $zone;

        if ($zoneUpdateStruct->linkedZone !== null) {
            // Linked zone other than a zone object indicates we want to remove the link
            $updatedZone->linkedLayoutId = null;
            $updatedZone->linkedZoneIdentifier = null;

            if ($zoneUpdateStruct->linkedZone instanceof Zone) {
                $updatedZone->linkedLayoutId = $zoneUpdateStruct->linkedZone->layoutId;
                $updatedZone->linkedZoneIdentifier = $zoneUpdateStruct->linkedZone->identifier;
            }
        }

        $this->queryHandler->updateZone($updatedZone);

        return $updatedZone;
    }

    public function copyLayout(Layout $layout, LayoutCopyStruct $layoutCopyStruct)
    {
        $copiedLayout = clone $layout;
        $copiedLayout->id = null;

        $currentTimeStamp = time();
        $copiedLayout->created = $currentTimeStamp;
        $copiedLayout->modified = $currentTimeStamp;

        if ($layoutCopyStruct->name !== null) {
            $copiedLayout->name = trim($layoutCopyStruct->name);
        }

        if ($layoutCopyStruct->description !== null) {
            $copiedLayout->description = trim($layoutCopyStruct->description);
        }

        $copiedLayout = $this->queryHandler->createLayout($copiedLayout);

        foreach ($copiedLayout->availableLocales as $locale) {
            $this->queryHandler->createLayoutTranslation($copiedLayout, $locale);
        }

        $layoutZones = $this->loadLayoutZones($layout);
        foreach ($layoutZones as $layoutZone) {
            $zoneCreateStruct = new ZoneCreateStruct(
                [
                    'identifier' => $layoutZone->identifier,
                    'linkedLayoutId' => $layoutZone->linkedLayoutId,
                    'linkedZoneIdentifier' => $layoutZone->linkedZoneIdentifier,
                ]
            );

            $createdZone = $this->createZone($copiedLayout, $zoneCreateStruct);
            $rootBlock = $this->blockHandler->loadBlock(
                $createdZone->rootBlockId,
                $createdZone->status
            );

            $zoneBlocks = $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock($layoutZone->rootBlockId, $layoutZone->status)
            );

            foreach ($zoneBlocks as $block) {
                $this->blockHandler->copyBlock($block, $rootBlock, 'root');
            }
        }

        return $copiedLayout;
    }

    public function changeLayoutType(Layout $layout, $targetLayoutType, array $zoneMappings = [])
    {
        $newRootBlocks = [];
        $oldRootBlocks = [];
        $oldZones = $this->loadLayoutZones($layout);

        foreach ($oldZones as $zoneIdentifier => $oldZone) {
            $oldRootBlocks[$zoneIdentifier] = $this->blockHandler->loadBlock(
                $oldZone->rootBlockId,
                $oldZone->status
            );
        }

        foreach ($zoneMappings as $newZoneIdentifier => $mappedZones) {
            $newRootBlocks[$newZoneIdentifier] = $this->blockHandler->createBlock(
                new BlockCreateStruct(
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
                    ]
                ),
                $layout
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
            $newZone = new Zone(
                [
                    'layoutId' => $layout->id,
                    'status' => $layout->status,
                    'rootBlockId' => $rootBlock->id,
                    'identifier' => $newZoneIdentifier,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ]
            );

            $this->queryHandler->createZone($newZone);
        }

        $newLayout = clone $layout;
        $newLayout->type = $targetLayoutType;
        $newLayout->modified = time();

        $this->queryHandler->updateLayout($newLayout);

        return $newLayout;
    }

    public function createLayoutStatus(Layout $layout, $newStatus)
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

    public function deleteLayout($layoutId, $status = null)
    {
        $this->queryHandler->deleteLayoutZones($layoutId, $status);
        $this->blockHandler->deleteLayoutBlocks($layoutId, $status);
        $this->queryHandler->deleteLayoutTranslations($layoutId, $status);
        $this->queryHandler->deleteLayout($layoutId, $status);
    }

    public function deleteLayoutTranslation(Layout $layout, $locale)
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
            } elseif (!empty($block->parentId)) {
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
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     */
    private function updateLayoutModifiedDate(Layout $layout)
    {
        $updatedLayout = clone $layout;
        $updatedLayout->modified = time();
        $this->queryHandler->updateLayout($updatedLayout);
    }
}
