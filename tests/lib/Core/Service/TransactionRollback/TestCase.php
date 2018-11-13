<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\BlockManager\Persistence\TransactionHandlerInterface;
use Netgen\BlockManager\Tests\Core\CoreTestCase;

/**
 * @property \PHPUnit\Framework\MockObject\MockObject $transactionHandler
 * @property \PHPUnit\Framework\MockObject\MockObject $layoutHandler
 * @property \PHPUnit\Framework\MockObject\MockObject $blockHandler
 * @property \PHPUnit\Framework\MockObject\MockObject $collectionHandler
 * @property \PHPUnit\Framework\MockObject\MockObject $layoutResolverHandler
 */
class TestCase extends CoreTestCase
{
    protected function createTransactionHandler(): TransactionHandlerInterface
    {
        return $this->createMock(TransactionHandlerInterface::class);
    }

    protected function createLayoutHandler(): LayoutHandlerInterface
    {
        return $this->createMock(LayoutHandlerInterface::class);
    }

    protected function createBlockHandler(): BlockHandlerInterface
    {
        return $this->createMock(BlockHandlerInterface::class);
    }

    protected function createCollectionHandler(): CollectionHandlerInterface
    {
        return $this->createMock(CollectionHandlerInterface::class);
    }

    protected function createLayoutResolverHandler(): LayoutResolverHandlerInterface
    {
        return $this->createMock(LayoutResolverHandlerInterface::class);
    }
}
