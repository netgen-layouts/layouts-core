<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\Persistence\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase as BaseServiceTestCase;
use Netgen\BlockManager\Persistence\Handler;

abstract class ServiceTestCase extends BaseServiceTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverHandlerMock;

    /**
     * Prepares the prerequisites for using services in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createMock(Handler::class);

        $this->blockHandlerMock = $this->createMock(BlockHandler::class);
        $this->layoutHandlerMock = $this->createMock(LayoutHandler::class);
        $this->collectionHandlerMock = $this->createMock(CollectionHandler::class);
        $this->layoutResolverHandlerMock = $this->createMock(LayoutResolverHandler::class);

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getBlockHandler')
            ->will($this->returnValue($this->blockHandlerMock));

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getLayoutHandler')
            ->will($this->returnValue($this->layoutHandlerMock));

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getCollectionHandler')
            ->will($this->returnValue($this->collectionHandlerMock));

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getLayoutResolverHandler')
            ->will($this->returnValue($this->layoutResolverHandlerMock));
    }
}
