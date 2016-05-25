<?php

namespace Netgen\BlockManager\API;

interface Repository
{
    /**
     * Returns the layout service.
     *
     * @return \Netgen\BlockManager\API\Service\LayoutService
     */
    public function getLayoutService();

    /**
     * Returns the block service.
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    public function getBlockService();

    /**
     * Returns the collection service.
     *
     * @return \Netgen\BlockManager\API\Service\CollectionService
     */
    public function getCollectionService();

    /**
     * Returns the layout resolver service.
     *
     * @return \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    public function getLayoutResolverService();

    /**
     * Begins a transaction.
     */
    public function beginTransaction();

    /**
     * Commits the transaction.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function commitTransaction();

    /**
     * Rollbacks the transaction.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function rollbackTransaction();
}
