<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Core\Values\Page\Layout;
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

        $this->layoutService = $this->createLayoutService($this->layoutValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Exception
     */
    public function testCreateLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->once())
            ->method('createLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->createLayout(new LayoutCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayoutStatus
     * @expectedException \Exception
     */
    public function testCreateLayoutStatus()
    {
        $this->layoutHandlerMock
            ->expects($this->once())
            ->method('createLayoutStatus')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->createLayoutStatus(new Layout(), Layout::STATUS_ARCHIVED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     * @expectedException \Exception
     */
    public function testPublishLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->once())
            ->method('deleteLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->publishLayout(new Layout(array('status' => Layout::STATUS_DRAFT)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Exception
     */
    public function testDeleteLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->once())
            ->method('deleteLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->deleteLayout(new Layout());
    }
}
