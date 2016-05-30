<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone as LayoutTypeZone;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler;
use Exception;

class LayoutServiceTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutHandlerMock;

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
        $this->preparePersistence();

        $this->layoutHandlerMock = $this->getMockBuilder(LayoutHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getLayoutHandler')
            ->will($this->returnValue($this->layoutHandlerMock));

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
        $this->layoutTypeRegistry->addLayoutType('3_zones_a', $layoutType);

        $this->layoutService = $this->createLayoutService(
            $this->layoutValidatorMock,
            $this->layoutTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Exception
     */
    public function testCreateLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('createLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->createLayout(new LayoutCreateStruct(array('type' => '3_zones_a')));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     * @expectedException \Exception
     */
    public function testCopyLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('copyLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->copyLayout(new Layout());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     * @expectedException \Exception
     */
    public function testCreateDraft()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(array('status' => Layout::STATUS_PUBLISHED))));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('layoutExists')
            ->will($this->returnValue(false));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->createDraft(new Layout());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     * @expectedException \Exception
     */
    public function testPublishLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(array('status' => Layout::STATUS_DRAFT))));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->publishLayout(new Layout());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Exception
     */
    public function testDeleteLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->deleteLayout(new Layout());
    }
}
