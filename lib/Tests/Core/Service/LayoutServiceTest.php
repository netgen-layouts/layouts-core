<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\LayoutUpdateStruct;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\Zone;

abstract class LayoutServiceTest extends ServiceTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutValidatorMock;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->layoutValidatorMock = $this->createMock(LayoutValidator::class);

        $this->layoutService = $this->createLayoutService($this->layoutValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     */
    public function testLoadLayout()
    {
        $layout = $this->layoutService->loadLayout(1);

        $this->assertEquals(Value::STATUS_PUBLISHED, $layout->getStatus());
        $this->assertInstanceOf(Layout::class, $layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
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

        $this->assertEquals(Value::STATUS_DRAFT, $layout->getStatus());
        $this->assertInstanceOf(Layout::class, $layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayoutDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
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
            $this->assertEquals(Value::STATUS_PUBLISHED, $layout->getStatus());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayouts
     */
    public function testLoadLayoutsWithUnpublishedLayouts()
    {
        $layouts = $this->layoutService->loadLayouts(true);

        $this->assertInternalType('array', $layouts);
        $this->assertCount(4, $layouts);

        foreach ($layouts as $layout) {
            $this->assertInstanceOf(Layout::class, $layout);
            $this->assertFalse($layout->isShared());

            if ($layout->getStatus() === Value::STATUS_DRAFT) {
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
            $this->assertEquals(Value::STATUS_PUBLISHED, $layout->getStatus());
        }
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

        $this->assertEquals(Value::STATUS_PUBLISHED, $zone->getStatus());
        $this->assertInstanceOf(Zone::class, $zone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingLayout()
    {
        $this->layoutService->loadZone(999999, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingZone()
    {
        $this->layoutService->loadZone(1, 'not_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     */
    public function testLoadZoneDraft()
    {
        $zone = $this->layoutService->loadZoneDraft(1, 'left');

        $this->assertEquals(Value::STATUS_DRAFT, $zone->getStatus());
        $this->assertInstanceOf(Zone::class, $zone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadZoneDraftThrowsNotFoundExceptionOnNonExistingLayout()
    {
        $this->layoutService->loadZoneDraft(999999, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZoneDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadZoneDraftThrowsNotFoundExceptionOnNonExistingZoneDraft()
    {
        $this->layoutService->loadZoneDraft(1, 'not_existing');
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
        $this->assertEquals(Value::STATUS_PUBLISHED, $updatedZone->getLinkedZone()->getStatus());
        $this->assertEquals($linkedZone->getLayoutId(), $updatedZone->getLinkedZone()->getLayoutId());
        $this->assertEquals($linkedZone->getIdentifier(), $updatedZone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testLinkZoneWhenInSharedLayout()
    {
        $zone = $this->layoutService->loadZoneDraft(3, 'left');
        $linkedZone = $this->layoutService->loadZone(5, 'left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
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
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout()
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            '4_zones_a',
            'My new layout'
        );

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        $this->assertEquals(Value::STATUS_DRAFT, $createdLayout->getStatus());
        $this->assertInstanceOf(Layout::class, $createdLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateLayoutThrowsBadStateException()
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            '4_zones_a',
            'My layout'
        );

        $this->layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     */
    public function testUpdateLayout()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';

        $updatedLayout = $this->layoutService->updateLayout($layout, $layoutUpdateStruct);

        $this->assertEquals(Value::STATUS_DRAFT, $updatedLayout->getStatus());
        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertEquals('New name', $updatedLayout->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateLayoutThrowsBadStateException()
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
        $layout = $this->layoutService->loadLayout(1);
        $copiedLayout = $this->layoutService->copyLayout($layout, 'New name');

        $this->assertEquals($layout->getStatus(), $copiedLayout->getStatus());
        $this->assertInstanceOf(Layout::class, $copiedLayout);

        $this->assertEquals(7, $copiedLayout->getId());
        $this->assertEquals('New name', $copiedLayout->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCopyLayoutThrowsBadStateException()
    {
        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->copyLayout($layout, 'My other layout');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraft()
    {
        $layout = $this->layoutService->loadLayout(6);
        $draftLayout = $this->layoutService->createDraft($layout);

        $this->assertEquals(Value::STATUS_DRAFT, $draftLayout->getStatus());
        $this->assertInstanceOf(Layout::class, $draftLayout);
        $this->assertGreaterThan($layout->getModified(), $draftLayout->getModified());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists()
    {
        $layout = $this->layoutService->loadLayout(1);
        $this->layoutService->createDraft($layout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDiscardDraft()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);
        $this->layoutService->discardDraft($layout);

        $this->layoutService->loadLayoutDraft($layout->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     */
    public function testPublishLayout()
    {
        $layout = $this->layoutService->loadLayoutDraft(1);
        $publishedLayout = $this->layoutService->publishLayout($layout);

        $this->assertInstanceOf(Layout::class, $publishedLayout);
        $this->assertEquals(Value::STATUS_PUBLISHED, $publishedLayout->getStatus());

        try {
            $this->layoutService->loadLayoutDraft($layout->getId());
            self::fail('Draft layout still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
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
                    'type' => '4_zones_a',
                    'name' => 'New layout',
                )
            ),
            $this->layoutService->newLayoutCreateStruct('4_zones_a', 'New layout')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutUpdateStruct
     */
    public function testNewLayoutUpdateStruct()
    {
        $this->assertEquals(
            new LayoutUpdateStruct(),
            $this->layoutService->newLayoutUpdateStruct()
        );
    }
}
