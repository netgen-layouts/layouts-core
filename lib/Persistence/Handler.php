<?php

namespace Netgen\BlockManager\Persistence;

interface Handler
{
    /**
     * Returns the layout handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Layout
     */
    public function getLayoutHandler();

    /**
     * Returns the block handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Block
     */
    public function getBlockHandler();

    /**
     * Returns the collection handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Collection
     */
    public function getCollectionHandler();

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
