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
 * @property \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\Persistence\TransactionHandlerInterface $transactionHandler
 * @property \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface $layoutHandler
 * @property \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\Persistence\Handler\BlockHandlerInterface $blockHandler
 * @property \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface $collectionHandler
 * @property \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface $layoutResolverHandler
 */
abstract class TestCase extends CoreTestCase
{
    final protected function createTransactionHandler(): TransactionHandlerInterface
    {
        return $this->createMock(TransactionHandlerInterface::class);
    }

    final protected function createLayoutHandler(): LayoutHandlerInterface
    {
        return $this->createMock(LayoutHandlerInterface::class);
    }

    final protected function createBlockHandler(): BlockHandlerInterface
    {
        return $this->createMock(BlockHandlerInterface::class);
    }

    final protected function createCollectionHandler(): CollectionHandlerInterface
    {
        return $this->createMock(CollectionHandlerInterface::class);
    }

    final protected function createLayoutResolverHandler(): LayoutResolverHandlerInterface
    {
        return $this->createMock(LayoutResolverHandlerInterface::class);
    }
}
