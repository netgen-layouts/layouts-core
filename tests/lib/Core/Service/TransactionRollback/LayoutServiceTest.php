<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Value;
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
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(PersistenceLayout::fromArray(['shared' => false])));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('loadZone')
            ->will(self::returnValue(PersistenceZone::fromArray(['layoutId' => 1])));

        $this->layoutHandlerMock
            ->expects(self::at(2))
            ->method('loadLayout')
            ->will(self::returnValue(PersistenceLayout::fromArray(['shared' => true])));

        $this->layoutHandlerMock
            ->expects(self::at(3))
            ->method('loadZone')
            ->will(self::returnValue(PersistenceZone::fromArray(['layoutId' => 2])));

        $this->layoutHandlerMock
            ->expects(self::at(4))
            ->method('updateZone')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->linkZone(
            Zone::fromArray(['identifier' => 'right', 'status' => Value::STATUS_DRAFT]),
            Zone::fromArray(['identifier' => 'left', 'status' => Value::STATUS_PUBLISHED])
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
            ->expects(self::at(0))
            ->method('loadZone')
            ->will(self::returnValue(new PersistenceZone()));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('updateZone')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->unlinkZone(Zone::fromArray(['identifier' => 'right', 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCreateLayout(): void
    {
        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('layoutNameExists')
            ->will(self::returnValue(false));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('createLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->name = 'Name';
        $layoutCreateStruct->mainLocale = 'en';
        $layoutCreateStruct->layoutType = LayoutType::fromArray(['identifier' => 'layout_type']);

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
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(
                self::returnValue(
                    PersistenceLayout::fromArray(
                        [
                            'mainLocale' => 'en',
                            'availableLocales' => ['en'],
                        ]
                    )
                )
            );

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('createLayoutTranslation')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->addTranslation(Layout::fromArray(['status' => Value::STATUS_DRAFT]), 'hr', 'en');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::removeTranslation
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRemoveTranslation(): void
    {
        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(
                self::returnValue(
                    PersistenceLayout::fromArray(
                        [
                            'mainLocale' => 'en',
                            'availableLocales' => ['en', 'hr'],
                        ]
                    )
                )
            );

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('deleteLayoutTranslation')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->removeTranslation(Layout::fromArray(['status' => Value::STATUS_DRAFT]), 'hr');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testUpdateLayout(): void
    {
        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('layoutNameExists')
            ->will(self::returnValue(false));

        $this->layoutHandlerMock
            ->expects(self::at(2))
            ->method('updateLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $layoutUpdateStruct = new LayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';

        $this->layoutService->updateLayout(
            Layout::fromArray(['status' => Value::STATUS_DRAFT]),
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
            ->expects(self::at(0))
            ->method('layoutNameExists')
            ->will(self::returnValue(false));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects(self::at(2))
            ->method('copyLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $layoutCopyStruct = new LayoutCopyStruct();
        $layoutCopyStruct->name = 'Name';

        $this->layoutService->copyLayout(
            Layout::fromArray(['id' => 42, 'status' => Layout::STATUS_DRAFT]),
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
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('loadLayoutZones')
            ->will(self::returnValue([]));

        $this->layoutHandlerMock
            ->expects(self::at(2))
            ->method('changeLayoutType')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->changeLayoutType(
            Layout::fromArray(['status' => Layout::STATUS_DRAFT]),
            LayoutType::fromArray(['identifier' => '4_zones_a']),
            []
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
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('layoutExists')
            ->will(self::returnValue(false));

        $this->layoutHandlerMock
            ->expects(self::at(2))
            ->method('deleteLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->createDraft(Layout::fromArray(['status' => Value::STATUS_PUBLISHED]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::discardDraft
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testDiscardDraft(): void
    {
        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('deleteLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->discardDraft(Layout::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testPublishLayout(): void
    {
        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('deleteLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->publishLayout(Layout::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::restoreFromArchive
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRestoreFromArchive(): void
    {
        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects(self::at(2))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects(self::at(3))
            ->method('deleteLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->restoreFromArchive(Layout::fromArray(['status' => Layout::STATUS_ARCHIVED]));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testDeleteLayout(): void
    {
        $this->layoutHandlerMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->will(self::returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects(self::at(1))
            ->method('deleteLayout')
            ->will(self::throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->layoutService->deleteLayout(Layout::fromArray(['id' => 42, 'status' => Layout::STATUS_DRAFT]));
    }
}
