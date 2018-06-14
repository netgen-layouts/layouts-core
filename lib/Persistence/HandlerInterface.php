<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence;

use Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface;

interface HandlerInterface
{
    /**
     * Returns the layout handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    public function getLayoutHandler(): LayoutHandlerInterface;

    /**
     * Returns the block handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface
     */
    public function getBlockHandler(): BlockHandlerInterface;

    /**
     * Returns the collection handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
     */
    public function getCollectionHandler(): CollectionHandlerInterface;

    /**
     * Returns the layout resolver handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface
     */
    public function getLayoutResolverHandler(): LayoutResolverHandlerInterface;

    /**
     * Begins the transaction.
     */
    public function beginTransaction(): void;

    /**
     * Commits the transaction.
     */
    public function commitTransaction(): void;

    /**
     * Rollbacks the transaction.
     */
    public function rollbackTransaction(): void;
}
