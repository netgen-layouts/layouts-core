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
