<?php

namespace Netgen\BlockManager\Persistence;

interface HandlerInterface
{
    /**
     * Returns the layout handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    public function getLayoutHandler();

    /**
     * Returns the block handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface
     */
    public function getBlockHandler();

    /**
     * Returns the collection handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
     */
    public function getCollectionHandler();

    /**
     * Returns the layout resolver handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface
     */
    public function getLayoutResolverHandler();

    /**
     * Begins the transaction.
     */
    public function beginTransaction();

    /**
     * Commits the transaction.
     */
    public function commitTransaction();

    /**
     * Rollbacks the transaction.
     */
    public function rollbackTransaction();
}
