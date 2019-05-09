<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\TransactionRollback;

use Netgen\Layouts\Persistence\Handler\BlockHandlerInterface;
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use Netgen\Layouts\Tests\Core\CoreTestCase;

/**
 * @property \PHPUnit\Framework\MockObject\MockObject $transactionHandler
 * @property \PHPUnit\Framework\MockObject\MockObject $layoutHandler
 * @property \PHPUnit\Framework\MockObject\MockObject $blockHandler
 * @property \PHPUnit\Framework\MockObject\MockObject $collectionHandler
 * @property \PHPUnit\Framework\MockObject\MockObject $layoutResolverHandler
 */
abstract class TestCase extends CoreTestCase
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
