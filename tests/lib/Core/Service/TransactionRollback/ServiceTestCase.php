<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\BlockManager\Persistence\HandlerInterface;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase as BaseServiceTestCase;

abstract class ServiceTestCase extends BaseServiceTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $persistenceHandler;

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
     * Prepares the prerequisites for using services in tests.
     */
    public function preparePersistence(): void
    {
        $this->persistenceHandler = $this->createMock(HandlerInterface::class);

        $this->blockHandlerMock = $this->createMock(BlockHandlerInterface::class);
        $this->layoutHandlerMock = $this->createMock(LayoutHandlerInterface::class);
        $this->collectionHandlerMock = $this->createMock(CollectionHandlerInterface::class);
        $this->layoutResolverHandlerMock = $this->createMock(LayoutResolverHandlerInterface::class);

        $this->persistenceHandler
            ->expects(self::any())
            ->method('getBlockHandler')
            ->will(self::returnValue($this->blockHandlerMock));

        $this->persistenceHandler
            ->expects(self::any())
            ->method('getLayoutHandler')
            ->will(self::returnValue($this->layoutHandlerMock));

        $this->persistenceHandler
            ->expects(self::any())
            ->method('getCollectionHandler')
            ->will(self::returnValue($this->collectionHandlerMock));

        $this->persistenceHandler
            ->expects(self::any())
            ->method('getLayoutResolverHandler')
            ->will(self::returnValue($this->layoutResolverHandlerMock));
    }
}
