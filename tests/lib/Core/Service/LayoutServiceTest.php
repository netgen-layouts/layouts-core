<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Type\LayoutType;

abstract class LayoutServiceTest extends ServiceTestCase
{
    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->layoutService = $this->createLayoutService();
        $this->blockService = $this->createBlockService();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     */
    public function testLoadLayout()
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
    public function testLoadLayoutThrowsNotFoundException()
    {
        $this->layoutService->loadLayout(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutDraft
     */
    public function testLoadLayoutDraft()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->assertFalse($layout->isPublished());
        $this->assertInstanceOf(Layout::class, $layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "999999"
     */
    public function testLoadLayoutDraftThrowsNotFoundException()
    {
        $this->layoutService->loadLayoutDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayouts
     */
    public function testLoadLayouts()
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
    public function testLoadLayoutsWithUnpublishedLayouts()
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
    public function testLoadSharedLayouts()
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
    public function testLoadRelatedLayouts()
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
    public function testLoadRelatedLayoutsThrowsBadStateExceptionWithNonPublishedSharedLayout()
    {
        $sharedLayout = $this->layoutService->loadLayoutDraft(3);
        $this->layoutService->loadRelatedLayouts($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadRelatedLayouts
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Related layouts can only be loaded for shared layouts.
     */
    public function testLoadRelatedLayoutsThrowsBadStateExceptionWithNonSharedLayout()
    {
        $sharedLayout = $this->layoutService->loadLayout(2);
        $this->layoutService->loadRelatedLayouts($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getRelatedLayoutsCount
     */
    public function testGetRelatedLayoutsCount()
    {
        $sharedLayout = $this->layoutService->loadLayout(3);
        $count = $this->layoutService->getRelatedLayoutsCount($sharedLayout);

        $this->assertEquals(1, $count);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getRelatedLayoutsCount
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Count of related layouts can only be loaded for published shared layouts.
     */
    public function testGetRelatedLayoutsCountThrowsBadStateExceptionWithNonPublishedSharedLayout()
    {
        $sharedLayout = $this->layoutService->loadLayoutDraft(3);
        $this->layoutService->getRelatedLayoutsCount($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::getRelatedLayoutsCount
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Count of related layouts can only be loaded for shared layouts.
     */
    public function testGetRelatedLayoutsCountThrowsBadStateExceptionWithNonSharedLayout()
    {
        $sharedLayout = $this->layoutService->loadLayout(2);
        $this->layoutService->getRelatedLayoutsCount($sharedLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::hasPublishedState
     */
    public function testHasPublishedState()
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->assertTrue($this->layoutService->hasPublishedState($layout));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::hasPublishedState
     */
    public function testHasPublishedStateReturnsFalse()
    {
        $layout = $this->layoutService->loadLayoutDraft(4);

        $this->assertFalse($this->layoutService->hasPublishedState($layout));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     */
    public function testLoadZone()
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
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingLayout()
    {
        $this->layoutService->loadZone(999999, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find zone with identifier "non_existing"
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingZone()
    {
        $this->layoutService->loadZone(1, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     */
    public function testLoadZoneDraft()
    {
        $zone = $this->layoutService->loadZoneDraft(1, 'left');

        $this->assertFalse($zone->isPublished());
        $this->assertInstanceOf(Zone::class, $zone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find zone with identifier "bottom"
     */
    public function testLoadZoneDraftThrowsNotFoundExceptionOnNonExistingLayout()
    {
        $this->layoutService->loadZoneDraft(999999, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find zone with identifier "non_existing"
     */
    public function testLoadZoneDraftThrowsNotFoundExceptionOnNonExistingZoneDraft()
    {
        $this->layoutService->loadZoneDraft(1, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameExists()
    {
        $this->assertTrue($this->layoutService->layoutNameExists('My layout'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameNotExistsWithExcludedLayoutId()
    {
        $this->assertFalse($this->layoutService->layoutNameExists('My layout', 1));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameNotExists()
    {
        $this->assertFalse($this->layoutService->layoutNameExists('Non existing'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     */
    public function testLinkZone()
    {
        $zone = $this->layoutService->loadZoneDraft(2, 'left');
        $linkedZone = $this->layoutService->loadZone(3, 'left');

        $updatedZone = $this->layoutService->linkZone($zone, $linkedZone);

        $this->assertInstanceOf(Zone::class, $updatedZone->getLinkedZone());
        $this->assertTrue($updatedZone->getLinkedZone()->isPublished());
        $this->assertEquals($linkedZone->getLayoutId(), $updatedZone->getLinkedZone()->getLayoutId());
        $this->assertEquals($linkedZone->getIdentifier(), $updatedZone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Zone cannot be in the shared layout.
     */
    public function testLinkZoneThrowsBadStateExceptionWhenInSharedLayout()
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
    public function testLinkZoneThrowsBadStateExceptionWithNonDraftZone()
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
    public function testLinkZoneThrowsBadStateExceptionWithNonPublishedLinkedZone()
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
    public function testLinkZoneThrowsBadStateExceptionWhenLinkedZoneNotInSharedLayout()
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
    public function testLinkZoneThrowsBadStateExceptionWhenInTheSameLayout()
    {
        $zone = $this->layoutService->loadZoneDraft(2, 'left');
        $linkedZone = $this->layoutService->loadZone(2, 'top');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::unlinkZone
     */
    public function testUnlinkZone()
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
    public function testUnlinkZoneThrowsBadStateExceptionWithNonDraftZone()
    {
        $zone = $this->layoutService->loadZone(2, 'top');

        $this->layoutService->unlinkZone($zone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout()
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            'My new layout',
            'en'
        );

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        $this->assertFalse($createdLayout->isPublished());
        $this->assertInstanceOf(Layout::class, $createdLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "name" has an invalid state. Layout with provided name already exists.
     */
    public function testCreateLayoutThrowsBadStateExceptionOnExistingName()
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
    public function testAddTranslation()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);
        $layout = $this->layoutService->addTranslation($layout, 'de');

        $this->assertEquals(array('en', 'hr', 'de'), $layout->getAvailableLocales());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($layout, true);
        foreach ($layoutBlocks as $layoutBlock) {
            $layoutBlock->isTranslatable() ?
                $this->assertContains('de', $layoutBlock->getAvailableLocales()) :
                $this->assertNotContains('de', $layoutBlock->getAvailableLocales());

            $layoutBlock->isTranslatable() ?
                $this->assertTrue($layoutBlock->hasTranslation('de')) :
                $this->assertFalse($layoutBlock->hasTranslation('de'));
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::addTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. You can only add translation to draft layouts.
     */
    public function testAddTranslationThrowsBadStateExceptionWithNonDraftLayout()
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->addTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::addTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Layout already has the provided locale.
     */
    public function testAddTranslationThrowsBadStateExceptionWithExistingLocale()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->layoutService->addTranslation($layout, 'en');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::setMainTranslation
     */
    public function testSetMainTranslation()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $layout = $this->layoutService->setMainTranslation($layout, 'hr');

        $this->assertEquals('hr', $layout->getMainLocale());
        $this->assertEquals(array('en', 'hr'), $layout->getAvailableLocales());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($layout, true);
        foreach ($layoutBlocks as $layoutBlock) {
            $this->assertEquals('hr', $layoutBlock->getMainLocale());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::setMainTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. You can only set main translation in draft layouts.
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonDraftLayout()
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->setMainTranslation($layout, 'hr');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::setMainTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "mainLocale" has an invalid state. Layout does not have the provided locale.
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->layoutService->setMainTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     */
    public function testRemoveTranslation()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $layout = $this->layoutService->removeTranslation($layout, 'hr');
        $this->assertNotContains('hr', $layout->getAvailableLocales());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($layout, true);
        foreach ($layoutBlocks as $layoutBlock) {
            $this->assertNotContains('hr', $layoutBlock->getAvailableLocales());
            $this->assertFalse($layoutBlock->hasTranslation('hr'));
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. You can only remove translations from draft layouts.
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithNonDraftLayout()
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->removeTranslation($layout, 'hr');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Layout does not have the provided locale.
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithNonExistingLocale()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->layoutService->removeTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Main translation cannot be removed from the layout.
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithMainLocale()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->layoutService->removeTranslation($layout, 'en');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     */
    public function testUpdateLayout()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';
        $layoutUpdateStruct->description = 'New description';

        $updatedLayout = $this->layoutService->updateLayout($layout, $layoutUpdateStruct);

        $this->assertFalse($updatedLayout->isPublished());
        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertEquals('New name', $updatedLayout->getName());
        $this->assertEquals('New description', $updatedLayout->getDescription());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Only draft layouts can be updated.
     */
    public function testUpdateLayoutThrowsBadStateExceptionWithNonDraftLayout()
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
    public function testUpdateLayoutThrowsBadStateExceptionWithExistingLayoutName()
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
    public function testCopyLayout()
    {
        $copyStruct = new LayoutCopyStruct();
        $copyStruct->name = 'New name';
        $copyStruct->description = 'New description';

        $layout = $this->layoutService->loadLayout(1);
        $copiedLayout = $this->layoutService->copyLayout($layout, $copyStruct);

        $this->assertEquals($layout->isPublished(), $copiedLayout->isPublished());
        $this->assertInstanceOf(Layout::class, $copiedLayout);

        $this->assertEquals(8, $copiedLayout->getId());
        $this->assertEquals('New name', $copiedLayout->getName());
        $this->assertEquals('New description', $copiedLayout->getDescription());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layoutCopyStruct" has an invalid state. Layout with provided name already exists.
     */
    public function testCopyLayoutThrowsBadStateExceptionOnExistingLayoutName()
    {
        $copyStruct = new LayoutCopyStruct();
        $copyStruct->name = 'My other layout';

        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->copyLayout($layout, $copyStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     */
    public function testChangeLayoutType()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b'),
            array(
                'top' => array('left', 'right'),
            )
        );

        $this->assertEquals($layout->getId(), $updatedLayout->getId());
        $this->assertEquals($layout->getStatus(), $updatedLayout->getStatus());
        $this->assertEquals('4_zones_b', $updatedLayout->getLayoutType()->getIdentifier());
        $this->assertInstanceOf(Layout::class, $updatedLayout);

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

        $this->assertEquals(32, $topZoneBlocks[0]->getId());
        $this->assertEquals(31, $topZoneBlocks[1]->getId());
        $this->assertEquals(35, $topZoneBlocks[2]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Layout type can only be changed for draft layouts.
     */
    public function testChangeLayoutTypeThrowsBadStateExceptionOnNonDraftLayout()
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::changeLayoutType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Layout is already of provided target type.
     */
    public function testChangeLayoutTypeThrowsBadStateExceptionOnSameType()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_a')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraft()
    {
        $layout = $this->layoutService->loadLayout(6);
        $draftLayout = $this->layoutService->createDraft($layout);

        $this->assertFalse($draftLayout->isPublished());
        $this->assertInstanceOf(Layout::class, $draftLayout);
        $this->assertGreaterThan($layout->getModified(), $draftLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraftWithDiscardingExistingDraft()
    {
        $layout = $this->layoutService->loadLayout(1);
        $draftLayout = $this->layoutService->createDraft($layout, true);

        $this->assertFalse($draftLayout->isPublished());
        $this->assertInstanceOf(Layout::class, $draftLayout);
        $this->assertGreaterThan($layout->getModified(), $draftLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Drafts can only be created from published layouts.
     */
    public function testCreateDraftThrowsBadStateExceptionWithNonPublishedLayout()
    {
        $layout = $this->layoutService->loadLayoutDraft(3);
        $this->layoutService->createDraft($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. The provided layout already has a draft.
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists()
    {
        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->createDraft($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "1"
     */
    public function testDiscardDraft()
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
    public function testDiscardDraftThrowsBadStateExceptionWithNonDraftLayout()
    {
        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->discardDraft($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     */
    public function testPublishLayout()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);
        $publishedLayout = $this->layoutService->publishLayout($layout);

        $this->assertInstanceOf(Layout::class, $publishedLayout);
        $this->assertTrue($publishedLayout->isPublished());

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
    public function testPublishLayoutThrowsBadStateExceptionWithNonDraftLayout()
    {
        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->publishLayout($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "1"
     */
    public function testDeleteLayout()
    {
        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->deleteLayout($layout);

        $this->layoutService->loadLayout($layout->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct()
    {
        $this->assertEquals(
            new LayoutCreateStruct(
                array(
                    'layoutType' => new LayoutType(array('identifier' => '4_zones_a')),
                    'name' => 'New layout',
                    'mainLocale' => 'en',
                )
            ),
            $this->layoutService->newLayoutCreateStruct(
                new LayoutType(array('identifier' => '4_zones_a')),
                'New layout',
                'en'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStruct()
    {
        $this->assertEquals(
            new LayoutUpdateStruct(
                array(
                    'name' => 'My layout',
                    'description' => 'My layout description',
                )
            ),
            $this->layoutService->newLayoutUpdateStruct(
                $this->layoutService->loadLayoutDraft(1)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStructWithNoLayout()
    {
        $this->assertEquals(
            new LayoutUpdateStruct(),
            $this->layoutService->newLayoutUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStruct()
    {
        $this->assertEquals(
            new LayoutCopyStruct(
                array(
                    'name' => 'My layout (copy)',
                    'description' => null,
                )
            ),
            $this->layoutService->newLayoutCopyStruct(
                $this->layoutService->loadLayoutDraft(1)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCopyStruct
     */
    public function testNewLayoutCopyStructWithNoLayout()
    {
        $this->assertEquals(
            new LayoutCopyStruct(),
            $this->layoutService->newLayoutCopyStruct()
        );
    }
}
