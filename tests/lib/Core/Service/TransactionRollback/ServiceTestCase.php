<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase as BaseServiceTestCase;

abstract class ServiceTestCase extends BaseServiceTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $blockHandlerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $layoutHandlerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $collectionHandlerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $layoutResolverHandlerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $persistenceHandler;

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
