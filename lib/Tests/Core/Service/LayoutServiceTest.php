<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone as LayoutTypeZone;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\LayoutDraft;
use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\BlockManager\API\Values\Page\ZoneDraft;

abstract class LayoutServiceTest extends ServiceTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutValidatorMock;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->layoutValidatorMock = $this->getMockBuilder(LayoutValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $layoutType = new LayoutType(
            '3_zones_a',
            true,
            '3 zones A',
            array(
                new LayoutTypeZone('left', 'Left', array()),
                new LayoutTypeZone('right', 'Right', array()),
                new LayoutTypeZone('bottom', 'Bottom', array()),
            )
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry();
        $this->layoutTypeRegistry->addLayoutType($layoutType);

        $this->layoutService = $this->createLayoutService(
            $this->layoutValidatorMock,
            $this->layoutTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     */
    public function testLoadLayout()
    {
        $layout = $this->layoutService->loadLayout(1);

        self::assertInstanceOf(Layout::class, $layout);
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

        self::assertInstanceOf(LayoutDraft::class, $layout);
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
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     */
    public function testLoadZone()
    {
        $zone = $this->layoutService->loadZone(1, 'top_left');

        self::assertInstanceOf(Zone::class, $zone);
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
        $zone = $this->layoutService->loadZoneDraft(1, 'top_left');

        self::assertInstanceOf(ZoneDraft::class, $zone);
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
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout()
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            '3_zones_a',
            'My layout'
        );

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        self::assertInstanceOf(LayoutDraft::class, $createdLayout);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testCreateLayoutThrowsInvalidArgumentException()
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            'non_existing',
            'My layout'
        );

        $this->layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     */
    public function testCopyLayout()
    {
        $layout = $this->layoutService->loadLayout(1);
        $copiedLayout = $this->layoutService->copyLayout($layout);

        self::assertInstanceOf(Layout::class, $copiedLayout);

        self::assertEquals(5, $copiedLayout->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraft()
    {
        $layout = $this->layoutService->loadLayout(3);
        $draftLayout = $this->layoutService->createDraft($layout);

        self::assertInstanceOf(LayoutDraft::class, $draftLayout);
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

        self::assertInstanceOf(Layout::class, $publishedLayout);
        self::assertEquals(Layout::STATUS_PUBLISHED, $publishedLayout->getStatus());

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
        self::assertEquals(
            new LayoutCreateStruct(
                array(
                    'type' => '3_zones_a',
                    'name' => 'New layout',
                )
            ),
            $this->layoutService->newLayoutCreateStruct('3_zones_a', 'New layout')
        );
    }
}
