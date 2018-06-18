<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service;

use DateTimeImmutable;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\TestCase\ExportObjectVarsTrait;

abstract class LayoutServiceTest extends ServiceTestCase
{
    use ExportObjectVarsTrait;

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

        $this->assertTrue($layout->isPublished());
        $this->assertInstanceOf(Layout::class, $layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "999999"
     */
    public function testLoadLayoutThrowsNotFoundException(): void
    {
        $this->layoutService->loadLayout(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutDraft
     */
    public function testLoadLayoutDraft(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->assertTrue($layout->isDraft());
        $this->assertInstanceOf(Layout::class, $layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "999999"
     */
    public function testLoadLayoutDraftThrowsNotFoundException(): void
    {
        $this->layoutService->loadLayoutDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutArchive
     */
    public function testLoadLayoutArchive(): void
    {
        $layout = $this->layoutService->loadLayoutArchive(2);

        $this->assertTrue($layout->isArchived());
        $this->assertInstanceOf(Layout::class, $layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutArchive
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "999999"
     */
    public function testLoadLayoutArchiveThrowsNotFoundException(): void
    {
        $this->layoutService->loadLayoutArchive(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayouts
     */
    public function testLoadLayouts(): void
    {
        $layouts = $this->layoutService->loadLayouts();

        $this->assertInternalType('array', $layouts);
        $this->assertCount(3, $layouts);

        foreach ($layouts as $layout) {
            $this->assertInstanceOf(Layout::class, $layout);
            $this->assertFalse($layout->isShared());
            $this->assertTrue($layout->isPublished());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayouts
     */
    public function testLoadLayoutsWithUnpublishedLayouts(): void
    {
        $layouts = $this->layoutService->loadLayouts(true);

        $this->assertInternalType('array', $layouts);
        $this->assertCount(5, $layouts);

        foreach ($layouts as $layout) {
            $this->assertInstanceOf(Layout::class, $layout);
            $this->assertFalse($layout->isShared());

            if (!$layout->isPublished()) {
                try {
                    $this->layoutService->loadLayout($layout->getId());
                    $this->fail('Layout in draft status with existing published version loaded.');
                } catch (NotFoundException $e) {
                    // Do nothing
                }
            }
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadSharedLayouts
     */
    public function testLoadSharedLayouts(): void
    {
        $layouts = $this->layoutService->loadSharedLayouts();

        $this->assertInternalType('array', $layouts);
        $this->assertCount(2, $layouts);

        foreach ($layouts as $layout) {
            $this->assertInstanceOf(Layout::class, $layout);
            $this->assertTrue($layout->isShared());
            $this->assertTrue($layout->isPublished());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadRelatedLayouts
     */
    public function testLoadRelatedLayouts(): void
    {
        $sharedLayout = $this->layoutService->loadLayout(3);
        $layouts = $this->layoutService->loadRelatedLayouts($sharedLayout);

        $this->assertInternalType('array', $layouts);
        $this->assertCount(1, $layouts);

        foreach ($layouts as $layout) {
            $this->assertInstanceOf(Layout::class, $layout);
            $this->assertFalse($layout->isShared());
            $this->assertTrue($layout->isPublished());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadRelatedLayouts
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Related layouts can only be loaded for published shared layouts.
     */
    public function testLoadRelatedLayoutsThrowsBadStateExceptionWithNonPublishedSharedLayout(): void
    {
        $sharedLayout = $this->layoutService->loadLayoutDraft(3);
        $this->layoutService->loadRelatedLayouts($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadRelatedLayouts
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Related layouts can only be loaded for shared layouts.
     */
    public function testLoadRelatedLayoutsThrowsBadStateExceptionWithNonSharedLayout(): void
    {
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

        $this->assertSame(1, $count);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getRelatedLayoutsCount
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Count of related layouts can only be loaded for published shared layouts.
     */
    public function testGetRelatedLayoutsCountThrowsBadStateExceptionWithNonPublishedSharedLayout(): void
    {
        $sharedLayout = $this->layoutService->loadLayoutDraft(3);
        $this->layoutService->getRelatedLayoutsCount($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getRelatedLayoutsCount
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Count of related layouts can only be loaded for shared layouts.
     */
    public function testGetRelatedLayoutsCountThrowsBadStateExceptionWithNonSharedLayout(): void
    {
        $sharedLayout = $this->layoutService->loadLayout(2);
        $this->layoutService->getRelatedLayoutsCount($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::hasStatus
     */
    public function testHasStatus(): void
    {
        $this->assertTrue($this->layoutService->hasStatus(1, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::hasStatus
     */
    public function testHasPublishedStateReturnsFalse(): void
    {
        $this->assertFalse($this->layoutService->hasStatus(4, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     */
    public function testLoadZone(): void
    {
        $zone = $this->layoutService->loadZone(1, 'left');

        $this->assertTrue($zone->isPublished());
        $this->assertInstanceOf(Zone::class, $zone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find zone with identifier "bottom"
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingLayout(): void
    {
        $this->layoutService->loadZone(999999, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find zone with identifier "non_existing"
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingZone(): void
    {
        $this->layoutService->loadZone(1, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     */
    public function testLoadZoneDraft(): void
    {
        $zone = $this->layoutService->loadZoneDraft(1, 'left');

        $this->assertTrue($zone->isDraft());
        $this->assertInstanceOf(Zone::class, $zone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find zone with identifier "bottom"
     */
    public function testLoadZoneDraftThrowsNotFoundExceptionOnNonExistingLayout(): void
    {
        $this->layoutService->loadZoneDraft(999999, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find zone with identifier "non_existing"
     */
    public function testLoadZoneDraftThrowsNotFoundExceptionOnNonExistingZoneDraft(): void
    {
        $this->layoutService->loadZoneDraft(1, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameExists(): void
    {
        $this->assertTrue($this->layoutService->layoutNameExists('My layout'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameNotExistsWithExcludedLayoutId(): void
    {
        $this->assertFalse($this->layoutService->layoutNameExists('My layout', 1));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameNotExists(): void
    {
        $this->assertFalse($this->layoutService->layoutNameExists('Non existing'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     */
    public function testLinkZone(): void
    {
        $zone = $this->layoutService->loadZoneDraft(2, 'left');
        $linkedZone = $this->layoutService->loadZone(3, 'left');

        $updatedZone = $this->layoutService->linkZone($zone, $linkedZone);

        $this->assertInstanceOf(Zone::class, $updatedZone->getLinkedZone());
        $this->assertTrue($updatedZone->getLinkedZone()->isPublished());
        $this->assertSame($linkedZone->getLayoutId(), $updatedZone->getLinkedZone()->getLayoutId());
        $this->assertSame($linkedZone->getIdentifier(), $updatedZone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Zone cannot be in the shared layout.
     */
    public function testLinkZoneThrowsBadStateExceptionWhenInSharedLayout(): void
    {
        $zone = $this->layoutService->loadZoneDraft(3, 'left');
        $linkedZone = $this->layoutService->loadZone(5, 'left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Only draft zones can be linked.
     */
    public function testLinkZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $zone = $this->layoutService->loadZone(2, 'left');
        $linkedZone = $this->layoutService->loadZone(3, 'left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "linkedZone" has an invalid state. Zones can only be linked to published zones.
     */
    public function testLinkZoneThrowsBadStateExceptionWithNonPublishedLinkedZone(): void
    {
        $zone = $this->layoutService->loadZoneDraft(2, 'left');
        $linkedZone = $this->layoutService->loadZoneDraft(3, 'left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "linkedZone" has an invalid state. Linked zone is not in the shared layout.
     */
    public function testLinkZoneThrowsBadStateExceptionWhenLinkedZoneNotInSharedLayout(): void
    {
        $zone = $this->layoutService->loadZoneDraft(2, 'left');
        $linkedZone = $this->layoutService->loadZone(1, 'left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "linkedZone" has an invalid state. Linked zone needs to be in a different layout.
     */
    public function testLinkZoneThrowsBadStateExceptionWhenInTheSameLayout(): void
    {
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

        $this->assertNull($updatedZone->getLinkedZone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::unlinkZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Only draft zones can be unlinked.
     */
    public function testUnlinkZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
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

        $this->assertTrue($createdLayout->isDraft());
        $this->assertInstanceOf(Layout::class, $createdLayout);

        $this->assertGreaterThan(new DateTimeImmutable('@0'), $createdLayout->getCreated());
        $this->assertEquals($createdLayout->getCreated(), $createdLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "name" has an invalid state. Layout with provided name already exists.
     */
    public function testCreateLayoutThrowsBadStateExceptionOnExistingName(): void
    {
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

        $this->assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $this->assertSame(['en', 'hr', 'de'], $updatedLayout->getAvailableLocales());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['en', 'hr', 'de']);
        foreach ($layoutBlocks as $layoutBlock) {
            $layoutBlock->isTranslatable() ?
                $this->assertContains('de', $layoutBlock->getAvailableLocales()) :
                $this->assertNotContains('de', $layoutBlock->getAvailableLocales());

            $layoutBlock->isTranslatable() ?
                $this->assertContains('de', $layoutBlock->getAvailableLocales()) :
                $this->assertNotContains('de', $layoutBlock->getAvailableLocales());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::addTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. You can only add translation to draft layouts.
     */
    public function testAddTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->addTranslation($layout, 'de', 'en');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::addTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Layout already has the provided locale.
     */
    public function testAddTranslationThrowsBadStateExceptionWithExistingLocale(): void
    {
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

        $this->assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $this->assertSame('hr', $updatedLayout->getMainLocale());
        $this->assertSame(['en', 'hr'], $updatedLayout->getAvailableLocales());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['hr', 'en']);
        foreach ($layoutBlocks as $layoutBlock) {
            $this->assertSame('hr', $layoutBlock->getMainLocale());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::setMainTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. You can only set main translation in draft layouts.
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->setMainTranslation($layout, 'hr');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::setMainTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "mainLocale" has an invalid state. Layout does not have the provided locale.
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
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
        $this->assertNotContains('hr', $updatedLayout->getAvailableLocales());

        $this->assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['en']);
        foreach ($layoutBlocks as $layoutBlock) {
            $this->assertNotContains('hr', $layoutBlock->getAvailableLocales());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. You can only remove translations from draft layouts.
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->removeTranslation($layout, 'hr');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Layout does not have the provided locale.
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->layoutService->removeTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Main translation cannot be removed from the layout.
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithMainLocale(): void
    {
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

        $this->assertTrue($updatedLayout->isDraft());
        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertSame('New name', $updatedLayout->getName());
        $this->assertSame('New description', $updatedLayout->getDescription());

        $this->assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $updatedLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Only draft layouts can be updated.
     */
    public function testUpdateLayoutThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $layout = $this->layoutService->loadLayout(1);

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';

        $this->layoutService->updateLayout($layout, $layoutUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "name" has an invalid state. Layout with provided name already exists.
     */
    public function testUpdateLayoutThrowsBadStateExceptionWithExistingLayoutName(): void
    {
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

        $this->assertSame($layout->isPublished(), $copiedLayout->isPublished());
        $this->assertInstanceOf(Layout::class, $copiedLayout);

        $this->assertGreaterThan($layout->getCreated(), $copiedLayout->getCreated());
        $this->assertEquals($copiedLayout->getCreated(), $copiedLayout->getModified());

        $this->assertSame(8, $copiedLayout->getId());
        $this->assertSame('New name', $copiedLayout->getName());
        $this->assertSame('New description', $copiedLayout->getDescription());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layoutCopyStruct" has an invalid state. Layout with provided name already exists.
     */
    public function testCopyLayoutThrowsBadStateExceptionOnExistingLayoutName(): void
    {
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

        $this->assertSame($layout->getId(), $updatedLayout->getId());
        $this->assertSame($layout->getStatus(), $updatedLayout->getStatus());
        $this->assertSame('4_zones_b', $updatedLayout->getLayoutType()->getIdentifier());
        $this->assertInstanceOf(Layout::class, $updatedLayout);

        $this->assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        $this->assertCount(3, $topZoneBlocks);
        $this->assertCount(0, $leftZoneBlocks);
        $this->assertCount(0, $rightZoneBlocks);
        $this->assertCount(0, $bottomZoneBlocks);

        $this->assertSame(32, $topZoneBlocks[0]->getId());
        $this->assertSame(31, $topZoneBlocks[1]->getId());
        $this->assertSame(35, $topZoneBlocks[2]->getId());
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

        $this->assertSame($layout->getId(), $updatedLayout->getId());
        $this->assertSame($layout->getStatus(), $updatedLayout->getStatus());
        $this->assertSame('4_zones_a', $updatedLayout->getLayoutType()->getIdentifier());
        $this->assertInstanceOf(Layout::class, $updatedLayout);

        $this->assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        $this->assertCount(3, $topZoneBlocks);
        $this->assertCount(0, $leftZoneBlocks);
        $this->assertCount(0, $rightZoneBlocks);
        $this->assertCount(0, $bottomZoneBlocks);

        $this->assertSame(32, $topZoneBlocks[0]->getId());
        $this->assertSame(31, $topZoneBlocks[1]->getId());
        $this->assertSame(35, $topZoneBlocks[2]->getId());
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

        $this->assertSame($layout->getId(), $updatedLayout->getId());
        $this->assertSame($layout->getStatus(), $updatedLayout->getStatus());
        $this->assertSame('4_zones_a', $updatedLayout->getLayoutType()->getIdentifier());
        $this->assertInstanceOf(Layout::class, $updatedLayout);

        $this->assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        $this->assertCount(0, $topZoneBlocks);
        $this->assertCount(0, $leftZoneBlocks);
        $this->assertCount(0, $rightZoneBlocks);
        $this->assertCount(0, $bottomZoneBlocks);

        $this->assertTrue($topZone->hasLinkedZone());

        $newTopZone = $layout->getZone('top', true);
        $this->assertInstanceOf(Zone::class, $newTopZone);

        $this->assertInstanceOf(Zone::class, $topZone->getLinkedZone());
        $this->assertInstanceOf(Zone::class, $newTopZone->getLinkedZone());

        $this->assertSame($newTopZone->getLinkedZone()->getLayoutId(), $topZone->getLinkedZone()->getLayoutId());
        $this->assertSame($newTopZone->getLinkedZone()->getIdentifier(), $topZone->getLinkedZone()->getIdentifier());
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

        $this->assertSame($layout->getId(), $updatedLayout->getId());
        $this->assertSame($layout->getStatus(), $updatedLayout->getStatus());
        $this->assertSame('4_zones_b', $updatedLayout->getLayoutType()->getIdentifier());
        $this->assertInstanceOf(Layout::class, $updatedLayout);

        $this->assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        $this->assertCount(0, $topZoneBlocks);
        $this->assertCount(0, $leftZoneBlocks);
        $this->assertCount(0, $rightZoneBlocks);
        $this->assertCount(0, $bottomZoneBlocks);

        $this->assertTrue($topZone->hasLinkedZone());

        $newTopZone = $layout->getZone('top', true);

        $this->assertInstanceOf(Zone::class, $newTopZone);

        $this->assertInstanceOf(Zone::class, $topZone->getLinkedZone());
        $this->assertInstanceOf(Zone::class, $newTopZone->getLinkedZone());

        $this->assertSame($newTopZone->getLinkedZone()->getLayoutId(), $topZone->getLinkedZone()->getLayoutId());
        $this->assertSame($newTopZone->getLinkedZone()->getIdentifier(), $topZone->getLinkedZone()->getIdentifier());
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

        $this->assertSame($layout->getId(), $updatedLayout->getId());
        $this->assertSame($layout->getStatus(), $updatedLayout->getStatus());
        $this->assertSame('4_zones_a', $updatedLayout->getLayoutType()->getIdentifier());
        $this->assertInstanceOf(Layout::class, $updatedLayout);

        $this->assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        $this->assertCount(0, $topZoneBlocks);
        $this->assertCount(0, $leftZoneBlocks);
        $this->assertCount(0, $rightZoneBlocks);
        $this->assertCount(0, $bottomZoneBlocks);

        $this->assertFalse($topZone->hasLinkedZone());
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

        $this->assertSame($layout->getId(), $updatedLayout->getId());
        $this->assertSame($layout->getStatus(), $updatedLayout->getStatus());
        $this->assertSame('4_zones_b', $updatedLayout->getLayoutType()->getIdentifier());
        $this->assertInstanceOf(Layout::class, $updatedLayout);

        $this->assertEquals($layout->getCreated(), $updatedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        $this->assertCount(0, $topZoneBlocks);
        $this->assertCount(0, $leftZoneBlocks);
        $this->assertCount(0, $rightZoneBlocks);
        $this->assertCount(0, $bottomZoneBlocks);

        $this->assertFalse($topZone->hasLinkedZone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Layout type can only be changed for draft layouts.
     */
    public function testChangeLayoutTypeThrowsBadStateExceptionOnNonDraftLayout(): void
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraft(): void
    {
        $layout = $this->layoutService->loadLayout(6);
        $draftLayout = $this->layoutService->createDraft($layout);

        $this->assertTrue($draftLayout->isDraft());
        $this->assertInstanceOf(Layout::class, $draftLayout);

        $this->assertEquals($layout->getCreated(), $draftLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $draftLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraftWithDiscardingExistingDraft(): void
    {
        $layout = $this->layoutService->loadLayout(1);
        $draftLayout = $this->layoutService->createDraft($layout, true);

        $this->assertTrue($draftLayout->isDraft());
        $this->assertInstanceOf(Layout::class, $draftLayout);

        $this->assertEquals($layout->getCreated(), $draftLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $draftLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Drafts can only be created from published layouts.
     */
    public function testCreateDraftThrowsBadStateExceptionWithNonPublishedLayout(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(3);
        $this->layoutService->createDraft($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. The provided layout already has a draft.
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists(): void
    {
        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->createDraft($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "1"
     */
    public function testDiscardDraft(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(1);
        $this->layoutService->discardDraft($layout);

        $this->layoutService->loadLayoutDraft($layout->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Only drafts can be discarded.
     */
    public function testDiscardDraftThrowsBadStateExceptionWithNonDraftLayout(): void
    {
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

        $this->assertInstanceOf(Layout::class, $publishedLayout);
        $this->assertTrue($publishedLayout->isPublished());

        $this->assertEquals($layout->getCreated(), $publishedLayout->getCreated());
        $this->assertGreaterThan($layout->getModified(), $publishedLayout->getModified());

        $archivedLayout = $this->layoutService->loadLayoutArchive(1);
        $this->assertEquals($currentlyPublishedLayout->getModified(), $archivedLayout->getModified());

        try {
            $this->layoutService->loadLayoutDraft($layout->getId());
            self::fail('Draft layout still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Only drafts can be published.
     */
    public function testPublishLayoutThrowsBadStateExceptionWithNonDraftLayout(): void
    {
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

        $this->assertInstanceOf(Layout::class, $restoredLayout);
        $this->assertTrue($restoredLayout->isDraft());
        $this->assertSame($publishedLayout->getName(), $restoredLayout->getName());

        $this->assertEquals($originalLayout->getCreated(), $restoredLayout->getCreated());
        $this->assertGreaterThan($originalLayout->getModified(), $restoredLayout->getModified());
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

        $this->assertInstanceOf(Layout::class, $restoredLayout);
        $this->assertTrue($restoredLayout->isDraft());
        $this->assertSame($publishedLayout->getName(), $restoredLayout->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::restoreFromArchive
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Only archived layouts can be restored.
     */
    public function testRestoreFromArchiveThrowsBadStateExceptionOnNonArchivedLayout(): void
    {
        $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayout(2)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::restoreFromArchive
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "2"
     */
    public function testRestoreFromArchiveThrowsNotFoundExceptionOnNonExistingPublishedVersion(): void
    {
        $this->persistenceHandler->getLayoutHandler()->deleteLayout(2, Layout::STATUS_PUBLISHED);
        $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayoutArchive(2)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "1"
     */
    public function testDeleteLayout(): void
    {
        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->deleteLayout($layout);

        $this->layoutService->loadLayout($layout->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct(): void
    {
        $layoutType = new LayoutType(['identifier' => '4_zones_a']);

        $struct = $this->layoutService->newLayoutCreateStruct(
            $layoutType,
            'New layout',
            'en'
        );

        $this->assertInstanceOf(LayoutCreateStruct::class, $struct);

        $this->assertSame(
            [
                'layoutType' => $layoutType,
                'name' => 'New layout',
                'description' => null,
                'shared' => false,
                'mainLocale' => 'en',
            ],
            $this->exportObjectVars($struct)
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

        $this->assertInstanceOf(LayoutUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'name' => 'My layout',
                'description' => 'My layout description',
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStructWithNoLayout(): void
    {
        $struct = $this->layoutService->newLayoutUpdateStruct();

        $this->assertInstanceOf(LayoutUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'name' => null,
                'description' => null,
            ],
            $this->exportObjectVars($struct)
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

        $this->assertInstanceOf(LayoutCopyStruct::class, $struct);

        $this->assertSame(
            [
                'name' => 'My layout (copy)',
                'description' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStructWithNoLayout(): void
    {
        $struct = $this->layoutService->newLayoutCopyStruct();

        $this->assertInstanceOf(LayoutCopyStruct::class, $struct);

        $this->assertSame(
            [
                'name' => null,
                'description' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }
}
