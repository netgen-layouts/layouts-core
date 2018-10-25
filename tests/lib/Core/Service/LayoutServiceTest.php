<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service;

use DateTimeImmutable;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\Core\CoreTestCase;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;

abstract class LayoutServiceTest extends CoreTestCase
{
    use ExportObjectTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->layoutService = $this->createLayoutService();
        $this->blockService = $this->createBlockService();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     */
    public function testLoadLayout(): void
    {
        $layout = $this->layoutService->loadLayout(1);

        self::assertTrue($layout->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     */
    public function testLoadLayoutThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "999999"');

        $this->layoutService->loadLayout(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutDraft
     */
    public function testLoadLayoutDraft(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        self::assertTrue($layout->isDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutDraft
     */
    public function testLoadLayoutDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "999999"');

        $this->layoutService->loadLayoutDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutArchive
     */
    public function testLoadLayoutArchive(): void
    {
        $layout = $this->layoutService->loadLayoutArchive(2);

        self::assertTrue($layout->isArchived());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutArchive
     */
    public function testLoadLayoutArchiveThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "999999"');

        $this->layoutService->loadLayoutArchive(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayouts
     */
    public function testLoadLayouts(): void
    {
        $layouts = $this->layoutService->loadLayouts();

        self::assertCount(3, $layouts);

        foreach ($layouts as $layout) {
            self::assertFalse($layout->isShared());
            self::assertTrue($layout->isPublished());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayouts
     */
    public function testLoadLayoutsWithUnpublishedLayouts(): void
    {
        $layouts = $this->layoutService->loadLayouts(true);

        self::assertCount(5, $layouts);

        foreach ($layouts as $layout) {
            self::assertFalse($layout->isShared());

            if (!$layout->isPublished()) {
                try {
                    $this->layoutService->loadLayout($layout->getId());
                    self::fail('Layout in draft status with existing published version loaded.');
                } catch (NotFoundException $e) {
                    // Do nothing
                }
            }
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getLayoutsCount
     */
    public function testGetLayoutsCount(): void
    {
        self::assertSame(3, $this->layoutService->getLayoutsCount());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getLayoutsCount
     */
    public function testGetLayoutsCountWithUnpublishedLayouts(): void
    {
        self::assertSame(5, $this->layoutService->getLayoutsCount(true));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadSharedLayouts
     */
    public function testLoadSharedLayouts(): void
    {
        $layouts = $this->layoutService->loadSharedLayouts();

        self::assertCount(2, $layouts);

        foreach ($layouts as $layout) {
            self::assertTrue($layout->isShared());
            self::assertTrue($layout->isPublished());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getSharedLayoutsCount
     */
    public function testGetSharedLayoutsCount(): void
    {
        self::assertSame(2, $this->layoutService->getSharedLayoutsCount());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadRelatedLayouts
     */
    public function testLoadRelatedLayouts(): void
    {
        $sharedLayout = $this->layoutService->loadLayout(3);
        $layouts = $this->layoutService->loadRelatedLayouts($sharedLayout);

        self::assertCount(1, $layouts);

        foreach ($layouts as $layout) {
            self::assertFalse($layout->isShared());
            self::assertTrue($layout->isPublished());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadRelatedLayouts
     */
    public function testLoadRelatedLayoutsThrowsBadStateExceptionWithNonPublishedSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Related layouts can only be loaded for published shared layouts.');

        $sharedLayout = $this->layoutService->loadLayoutDraft(3);
        $this->layoutService->loadRelatedLayouts($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadRelatedLayouts
     */
    public function testLoadRelatedLayoutsThrowsBadStateExceptionWithNonSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Related layouts can only be loaded for shared layouts.');

        $sharedLayout = $this->layoutService->loadLayout(2);
        $this->layoutService->loadRelatedLayouts($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getRelatedLayoutsCount
     */
    public function testGetRelatedLayoutsCount(): void
    {
        $sharedLayout = $this->layoutService->loadLayout(3);
        $count = $this->layoutService->getRelatedLayoutsCount($sharedLayout);

        self::assertSame(1, $count);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getRelatedLayoutsCount
     */
    public function testGetRelatedLayoutsCountThrowsBadStateExceptionWithNonPublishedSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Count of related layouts can only be loaded for published shared layouts.');

        $sharedLayout = $this->layoutService->loadLayoutDraft(3);
        $this->layoutService->getRelatedLayoutsCount($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getRelatedLayoutsCount
     */
    public function testGetRelatedLayoutsCountThrowsBadStateExceptionWithNonSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Count of related layouts can only be loaded for shared layouts.');

        $sharedLayout = $this->layoutService->loadLayout(2);
        $this->layoutService->getRelatedLayoutsCount($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::hasStatus
     */
    public function testHasStatus(): void
    {
        self::assertTrue($this->layoutService->hasStatus(1, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::hasStatus
     */
    public function testHasPublishedStateReturnsFalse(): void
    {
        self::assertFalse($this->layoutService->hasStatus(4, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     */
    public function testLoadZone(): void
    {
        $zone = $this->layoutService->loadZone(1, 'left');

        self::assertTrue($zone->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingLayout(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find zone with identifier "bottom"');

        $this->layoutService->loadZone(999999, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingZone(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find zone with identifier "non_existing"');

        $this->layoutService->loadZone(1, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     */
    public function testLoadZoneDraft(): void
    {
        $zone = $this->layoutService->loadZoneDraft(1, 'left');

        self::assertTrue($zone->isDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     */
    public function testLoadZoneDraftThrowsNotFoundExceptionOnNonExistingLayout(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find zone with identifier "bottom"');

        $this->layoutService->loadZoneDraft(999999, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     */
    public function testLoadZoneDraftThrowsNotFoundExceptionOnNonExistingZoneDraft(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find zone with identifier "non_existing"');

        $this->layoutService->loadZoneDraft(1, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameExists(): void
    {
        self::assertTrue($this->layoutService->layoutNameExists('My layout'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameNotExistsWithExcludedLayoutId(): void
    {
        self::assertFalse($this->layoutService->layoutNameExists('My layout', 1));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameNotExists(): void
    {
        self::assertFalse($this->layoutService->layoutNameExists('Non existing'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     */
    public function testLinkZone(): void
    {
        $zone = $this->layoutService->loadZoneDraft(2, 'left');
        $linkedZone = $this->layoutService->loadZone(3, 'left');

        $updatedZone = $this->layoutService->linkZone($zone, $linkedZone);

        self::assertInstanceOf(Zone::class, $updatedZone->getLinkedZone());
        self::assertTrue($updatedZone->getLinkedZone()->isPublished());
        self::assertSame($linkedZone->getLayoutId(), $updatedZone->getLinkedZone()->getLayoutId());
        self::assertSame($linkedZone->getIdentifier(), $updatedZone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     */
    public function testLinkZoneThrowsBadStateExceptionWhenInSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Zone cannot be in the shared layout.');

        $zone = $this->layoutService->loadZoneDraft(3, 'left');
        $linkedZone = $this->layoutService->loadZone(5, 'left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     */
    public function testLinkZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Only draft zones can be linked.');

        $zone = $this->layoutService->loadZone(2, 'left');
        $linkedZone = $this->layoutService->loadZone(3, 'left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     */
    public function testLinkZoneThrowsBadStateExceptionWithNonPublishedLinkedZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "linkedZone" has an invalid state. Zones can only be linked to published zones.');

        $zone = $this->layoutService->loadZoneDraft(2, 'left');
        $linkedZone = $this->layoutService->loadZoneDraft(3, 'left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     */
    public function testLinkZoneThrowsBadStateExceptionWhenLinkedZoneNotInSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "linkedZone" has an invalid state. Linked zone is not in the shared layout.');

        $zone = $this->layoutService->loadZoneDraft(2, 'left');
        $linkedZone = $this->layoutService->loadZone(1, 'left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     */
    public function testLinkZoneThrowsBadStateExceptionWhenInTheSameLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "linkedZone" has an invalid state. Linked zone needs to be in a different layout.');

        $zone = $this->layoutService->loadZoneDraft(2, 'left');
        $linkedZone = $this->layoutService->loadZone(2, 'top');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::unlinkZone
     */
    public function testUnlinkZone(): void
    {
        $zone = $this->layoutService->loadZoneDraft(2, 'top');

        $updatedZone = $this->layoutService->unlinkZone($zone);

        self::assertNull($updatedZone->getLinkedZone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::unlinkZone
     */
    public function testUnlinkZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Only draft zones can be unlinked.');

        $zone = $this->layoutService->loadZone(2, 'top');

        $this->layoutService->unlinkZone($zone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout(): void
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            'My new layout',
            'en'
        );

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        self::assertTrue($createdLayout->isDraft());

        self::assertGreaterThan(new DateTimeImmutable('@0'), $createdLayout->getCreated());
        self::assertEquals($createdLayout->getCreated(), $createdLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayoutThrowsBadStateExceptionOnExistingName(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "name" has an invalid state. Layout with provided name already exists.');

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            'My layout',
            'en'
        );

        $this->layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::addTranslation
     */
    public function testAddTranslation(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);
        $updatedLayout = $this->layoutService->addTranslation($layout, 'de', 'en');

        self::assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        self::assertSame(['en', 'hr', 'de'], $updatedLayout->getAvailableLocales());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['en', 'hr', 'de']);
        foreach ($layoutBlocks as $layoutBlock) {
            $layoutBlock->isTranslatable() ?
                self::assertContains('de', $layoutBlock->getAvailableLocales()) :
                self::assertNotContains('de', $layoutBlock->getAvailableLocales());

            $layoutBlock->isTranslatable() ?
                self::assertContains('de', $layoutBlock->getAvailableLocales()) :
                self::assertNotContains('de', $layoutBlock->getAvailableLocales());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::addTranslation
     */
    public function testAddTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. You can only add translation to draft layouts.');

        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->addTranslation($layout, 'de', 'en');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::addTranslation
     */
    public function testAddTranslationThrowsBadStateExceptionWithExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Layout already has the provided locale.');

        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->layoutService->addTranslation($layout, 'en', 'hr');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::setMainTranslation
     */
    public function testSetMainTranslation(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $updatedLayout = $this->layoutService->setMainTranslation($layout, 'hr');

        self::assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        self::assertSame('hr', $updatedLayout->getMainLocale());
        self::assertSame(['en', 'hr'], $updatedLayout->getAvailableLocales());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['hr', 'en']);
        foreach ($layoutBlocks as $layoutBlock) {
            self::assertSame('hr', $layoutBlock->getMainLocale());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::setMainTranslation
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. You can only set main translation in draft layouts.');

        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->setMainTranslation($layout, 'hr');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::setMainTranslation
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "mainLocale" has an invalid state. Layout does not have the provided locale.');

        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->layoutService->setMainTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     */
    public function testRemoveTranslation(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $updatedLayout = $this->layoutService->removeTranslation($layout, 'hr');
        self::assertNotContains('hr', $updatedLayout->getAvailableLocales());

        self::assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['en']);
        foreach ($layoutBlocks as $layoutBlock) {
            self::assertNotContains('hr', $layoutBlock->getAvailableLocales());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. You can only remove translations from draft layouts.');

        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->removeTranslation($layout, 'hr');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Layout does not have the provided locale.');

        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->layoutService->removeTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithMainLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Main translation cannot be removed from the layout.');

        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->layoutService->removeTranslation($layout, 'en');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     */
    public function testUpdateLayout(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';
        $layoutUpdateStruct->description = 'New description';

        $updatedLayout = $this->layoutService->updateLayout($layout, $layoutUpdateStruct);

        self::assertTrue($updatedLayout->isDraft());
        self::assertSame('New name', $updatedLayout->getName());
        self::assertSame('New description', $updatedLayout->getDescription());

        self::assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     */
    public function testUpdateLayoutThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only draft layouts can be updated.');

        $layout = $this->layoutService->loadLayout(1);

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';

        $this->layoutService->updateLayout($layout, $layoutUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     */
    public function testUpdateLayoutThrowsBadStateExceptionWithExistingLayoutName(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "name" has an invalid state. Layout with provided name already exists.');

        $layout = $this->layoutService->loadLayoutDraft(2);

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'My layout';

        $this->layoutService->updateLayout(
            $layout,
            $layoutUpdateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     */
    public function testCopyLayout(): void
    {
        $copyStruct = new LayoutCopyStruct();
        $copyStruct->name = 'New name';
        $copyStruct->description = 'New description';

        $layout = $this->layoutService->loadLayout(1);
        $copiedLayout = $this->layoutService->copyLayout($layout, $copyStruct);

        self::assertSame($layout->isPublished(), $copiedLayout->isPublished());

        self::assertGreaterThan($layout->getCreated(), $copiedLayout->getCreated());
        self::assertEquals($copiedLayout->getCreated(), $copiedLayout->getModified());

        self::assertSame(8, $copiedLayout->getId());
        self::assertSame('New name', $copiedLayout->getName());
        self::assertSame('New description', $copiedLayout->getDescription());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     */
    public function testCopyLayoutThrowsBadStateExceptionOnExistingLayoutName(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layoutCopyStruct" has an invalid state. Layout with provided name already exists.');

        $copyStruct = new LayoutCopyStruct();
        $copyStruct->name = 'My other layout';

        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->copyLayout($layout, $copyStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     */
    public function testChangeLayoutType(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b'),
            [
                'top' => ['left', 'right'],
            ]
        );

        self::assertSame($layout->getId(), $updatedLayout->getId());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_b', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $topZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(1, 'top')
        );

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(1, 'left')
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(1, 'right')
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(1, 'bottom')
        );

        self::assertCount(3, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertSame(32, $topZoneBlocks[0]->getId());
        self::assertSame(31, $topZoneBlocks[1]->getId());
        self::assertSame(35, $topZoneBlocks[2]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     */
    public function testChangeLayoutTypeWithSameLayoutType(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            [
                'top' => ['left', 'right'],
            ]
        );

        self::assertSame($layout->getId(), $updatedLayout->getId());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_a', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $topZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(1, 'top')
        );

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(1, 'left')
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(1, 'right')
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(1, 'bottom')
        );

        self::assertCount(3, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertSame(32, $topZoneBlocks[0]->getId());
        self::assertSame(31, $topZoneBlocks[1]->getId());
        self::assertSame(35, $topZoneBlocks[2]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     */
    public function testChangeLayoutTypeWithSharedZones(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(2);
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            [
                'top' => ['top'],
            ]
        );

        self::assertSame($layout->getId(), $updatedLayout->getId());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_a', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $topZone = $this->layoutService->loadZoneDraft(2, 'top');
        $topZoneBlocks = $this->blockService->loadZoneBlocks($topZone);

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'left')
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'right')
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'bottom')
        );

        self::assertCount(0, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertTrue($topZone->hasLinkedZone());

        $newTopZone = $layout->getZone('top');
        self::assertInstanceOf(Zone::class, $newTopZone);

        self::assertInstanceOf(Zone::class, $topZone->getLinkedZone());
        self::assertInstanceOf(Zone::class, $newTopZone->getLinkedZone());

        self::assertSame($newTopZone->getLinkedZone()->getLayoutId(), $topZone->getLinkedZone()->getLayoutId());
        self::assertSame($newTopZone->getLinkedZone()->getIdentifier(), $topZone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     */
    public function testChangeLayoutTypeWithSameLayoutTypeAndSharedZones(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(2);
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b'),
            [
                'top' => ['top'],
            ]
        );

        self::assertSame($layout->getId(), $updatedLayout->getId());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_b', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $topZone = $this->layoutService->loadZoneDraft(2, 'top');
        $topZoneBlocks = $this->blockService->loadZoneBlocks($topZone);

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'left')
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'right')
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'bottom')
        );

        self::assertCount(0, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertTrue($topZone->hasLinkedZone());

        $newTopZone = $layout->getZone('top');
        self::assertInstanceOf(Zone::class, $newTopZone);

        self::assertInstanceOf(Zone::class, $topZone->getLinkedZone());
        self::assertInstanceOf(Zone::class, $newTopZone->getLinkedZone());

        self::assertSame($newTopZone->getLinkedZone()->getLayoutId(), $topZone->getLinkedZone()->getLayoutId());
        self::assertSame($newTopZone->getLinkedZone()->getIdentifier(), $topZone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     */
    public function testChangeLayoutTypeWithSharedZonesAndDiscardingSharedZones(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(2);
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            [
                'top' => ['top'],
            ],
            false
        );

        self::assertSame($layout->getId(), $updatedLayout->getId());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_a', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $topZone = $this->layoutService->loadZoneDraft(2, 'top');
        $topZoneBlocks = $this->blockService->loadZoneBlocks($topZone);

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'left')
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'right')
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'bottom')
        );

        self::assertCount(0, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertFalse($topZone->hasLinkedZone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     */
    public function testChangeLayoutTypeWithSameLayoutTypeAndSharedZonesAndDiscardingSharedZones(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(2);
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b'),
            [
                'top' => ['top'],
            ],
            false
        );

        self::assertSame($layout->getId(), $updatedLayout->getId());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_b', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $topZone = $this->layoutService->loadZoneDraft(2, 'top');
        $topZoneBlocks = $this->blockService->loadZoneBlocks($topZone);

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'left')
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'right')
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZoneDraft(2, 'bottom')
        );

        self::assertCount(0, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertFalse($topZone->hasLinkedZone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     */
    public function testChangeLayoutTypeThrowsBadStateExceptionOnNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Layout type can only be changed for draft layouts.');

        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b'),
            []
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraft(): void
    {
        $layout = $this->layoutService->loadLayout(6);
        $draftLayout = $this->layoutService->createDraft($layout);

        self::assertTrue($draftLayout->isDraft());

        self::assertEquals($layout->getCreated(), $draftLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $draftLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraftWithDiscardingExistingDraft(): void
    {
        $layout = $this->layoutService->loadLayout(1);
        $draftLayout = $this->layoutService->createDraft($layout, true);

        self::assertTrue($draftLayout->isDraft());

        self::assertEquals($layout->getCreated(), $draftLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $draftLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraftThrowsBadStateExceptionWithNonPublishedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Drafts can only be created from published layouts.');

        $layout = $this->layoutService->loadLayoutDraft(3);
        $this->layoutService->createDraft($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. The provided layout already has a draft.');

        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->createDraft($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::discardDraft
     */
    public function testDiscardDraft(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "1"');

        $layout = $this->layoutService->loadLayoutDraft(1);
        $this->layoutService->discardDraft($layout);

        $this->layoutService->loadLayoutDraft($layout->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::discardDraft
     */
    public function testDiscardDraftThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only drafts can be discarded.');

        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->discardDraft($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     */
    public function testPublishLayout(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);
        $currentlyPublishedLayout = $this->layoutService->loadLayout(1);
        $publishedLayout = $this->layoutService->publishLayout($layout);

        self::assertTrue($publishedLayout->isPublished());

        self::assertEquals($layout->getCreated(), $publishedLayout->getCreated());
        self::assertGreaterThan($layout->getModified(), $publishedLayout->getModified());

        $archivedLayout = $this->layoutService->loadLayoutArchive(1);
        self::assertEquals($currentlyPublishedLayout->getModified(), $archivedLayout->getModified());

        try {
            $this->layoutService->loadLayoutDraft($layout->getId());
            self::fail('Draft layout still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     */
    public function testPublishLayoutThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only drafts can be published.');

        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->publishLayout($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::restoreFromArchive
     */
    public function testRestoreFromArchive(): void
    {
        $originalLayout = $this->layoutService->loadLayoutDraft(2);
        $publishedLayout = $this->layoutService->loadLayout(2);
        $restoredLayout = $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayoutArchive(2)
        );

        self::assertTrue($restoredLayout->isDraft());
        self::assertSame($publishedLayout->getName(), $restoredLayout->getName());

        self::assertEquals($originalLayout->getCreated(), $restoredLayout->getCreated());
        self::assertGreaterThan($originalLayout->getModified(), $restoredLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::restoreFromArchive
     */
    public function testRestoreFromArchiveWithoutDraft(): void
    {
        $originalLayout = $this->layoutService->loadLayoutDraft(2);
        $this->layoutService->discardDraft($originalLayout);

        $publishedLayout = $this->layoutService->loadLayout(2);
        $restoredLayout = $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayoutArchive(2)
        );

        self::assertTrue($restoredLayout->isDraft());
        self::assertSame($publishedLayout->getName(), $restoredLayout->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::restoreFromArchive
     */
    public function testRestoreFromArchiveThrowsBadStateExceptionOnNonArchivedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Only archived layouts can be restored.');

        $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayout(2)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::restoreFromArchive
     */
    public function testRestoreFromArchiveThrowsNotFoundExceptionOnNonExistingPublishedVersion(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "2"');

        $this->persistenceHandler->getLayoutHandler()->deleteLayout(2, Layout::STATUS_PUBLISHED);
        $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayoutArchive(2)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     */
    public function testDeleteLayout(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "1"');

        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->deleteLayout($layout);

        $this->layoutService->loadLayout($layout->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct(): void
    {
        $layoutType = LayoutType::fromArray(['identifier' => '4_zones_a']);

        $struct = $this->layoutService->newLayoutCreateStruct(
            $layoutType,
            'New layout',
            'en'
        );

        self::assertSame(
            [
                'layoutType' => $layoutType,
                'name' => 'New layout',
                'description' => null,
                'shared' => false,
                'mainLocale' => 'en',
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStruct(): void
    {
        $struct = $this->layoutService->newLayoutUpdateStruct(
            $this->layoutService->loadLayoutDraft(1)
        );

        self::assertSame(
            [
                'name' => 'My layout',
                'description' => 'My layout description',
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStructWithNoLayout(): void
    {
        $struct = $this->layoutService->newLayoutUpdateStruct();

        self::assertSame(
            [
                'name' => null,
                'description' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStruct(): void
    {
        $struct = $this->layoutService->newLayoutCopyStruct(
            $this->layoutService->loadLayoutDraft(1)
        );

        self::assertSame(
            [
                'name' => 'My layout (copy)',
                'description' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStructWithNoLayout(): void
    {
        $struct = $this->layoutService->newLayoutCopyStruct();

        self::assertSame(
            [
                'name' => null,
                'description' => null,
            ],
            $this->exportObject($struct)
        );
    }
}
