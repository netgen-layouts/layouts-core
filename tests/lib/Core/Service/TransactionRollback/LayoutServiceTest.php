<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;

final class LayoutServiceTest extends ServiceTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->layoutService = $this->createLayoutService();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testLinkZone(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(['shared' => false])));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone(['layoutId' => 1])));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(['shared' => true])));

        $this->layoutHandlerMock
            ->expects($this->at(3))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone(['layoutId' => 2])));

        $this->layoutHandlerMock
            ->expects($this->at(4))
            ->method('updateZone')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->linkZone(
            new Zone(['identifier' => 'right', 'status' => Value::STATUS_DRAFT]),
            new Zone(['identifier' => 'left', 'status' => Value::STATUS_PUBLISHED])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::unlinkZone
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testUnlinkZone(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('updateZone')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->unlinkZone(new Zone(['identifier' => 'right', 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCreateLayout(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('layoutNameExists')
            ->will($this->returnValue(false));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('createLayout')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->name = 'Name';
        $layoutCreateStruct->mainLocale = 'en';
        $layoutCreateStruct->layoutType = new LayoutType(['identifier' => 'layout_type']);

        $this->layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::addTranslation
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testAddTranslation(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will(
                $this->returnValue(
                    new PersistenceLayout(
                        [
                            'mainLocale' => 'en',
                            'availableLocales' => ['en'],
                        ]
                    )
                )
            );

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('createLayoutTranslation')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->addTranslation(new Layout(['status' => Value::STATUS_DRAFT]), 'hr', 'en');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRemoveTranslation(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will(
                $this->returnValue(
                    new PersistenceLayout(
                        [
                            'mainLocale' => 'en',
                            'availableLocales' => ['en', 'hr'],
                        ]
                    )
                )
            );

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('deleteLayoutTranslation')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->removeTranslation(new Layout(['status' => Value::STATUS_DRAFT]), 'hr');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testUpdateLayout(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('layoutNameExists')
            ->will($this->returnValue(false));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('updateLayout')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $layoutUpdateStruct = new LayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';

        $this->layoutService->updateLayout(
            new Layout(['status' => Value::STATUS_DRAFT]),
            $layoutUpdateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCopyLayout(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('layoutNameExists')
            ->will($this->returnValue(false));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('copyLayout')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $layoutCopyStruct = new LayoutCopyStruct();
        $layoutCopyStruct->name = 'Name';

        $this->layoutService->copyLayout(
            new Layout(['id' => 42, 'status' => Layout::STATUS_DRAFT]),
            $layoutCopyStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testChangeLayoutType(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('loadLayoutZones')
            ->will($this->returnValue([]));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('changeLayoutType')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->changeLayoutType(
            new Layout(['status' => Layout::STATUS_DRAFT]),
            new LayoutType(['identifier' => '4_zones_a'])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCreateDraft(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('layoutExists')
            ->will($this->returnValue(false));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->createDraft(new Layout(['status' => Value::STATUS_PUBLISHED]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::discardDraft
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testDiscardDraft(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->discardDraft(new Layout(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testPublishLayout(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->publishLayout(new Layout(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::restoreFromArchive
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRestoreFromArchive(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(3))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->restoreFromArchive(new Layout(['status' => Layout::STATUS_ARCHIVED]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testDeleteLayout(): void
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->deleteLayout(new Layout(['id' => 42, 'status' => Layout::STATUS_DRAFT]));
    }
}
