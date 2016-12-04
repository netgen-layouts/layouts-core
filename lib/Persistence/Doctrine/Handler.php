<?php

namespace Netgen\BlockManager\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Netgen\BlockManager\Persistence\Handler as HandlerInterface;
use Netgen\BlockManager\Persistence\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler;

class Handler implements HandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandler
     */
    protected $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandler
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler
     */
    protected $layoutResolverHandler;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Persistence\Handler\LayoutHandler $layoutHandler
     * @param \Netgen\BlockManager\Persistence\Handler\BlockHandler $blockHandler
     * @param \Netgen\BlockManager\Persistence\Handler\CollectionHandler $collectionHandler
     * @param \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler $layoutResolverHandler
     */
    public function __construct(
        Connection $connection,
        LayoutHandler $layoutHandler,
        BlockHandler $blockHandler,
        CollectionHandler $collectionHandler,
        LayoutResolverHandler $layoutResolverHandler
    ) {
        $this->connection = $connection;
        $this->layoutHandler = $layoutHandler;
        $this->blockHandler = $blockHandler;
        $this->collectionHandler = $collectionHandler;
        $this->layoutResolverHandler = $layoutResolverHandler;
    }

    /**
     * Returns the layout handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\LayoutHandler
     */
    public function getLayoutHandler()
    {
        return $this->layoutHandler;
    }

    /**
     * Returns the block handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\BlockHandler
     */
    public function getBlockHandler()
    {
        return $this->blockHandler;
    }

    /**
     * Returns the collection handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    public function getCollectionHandler()
    {
        return $this->collectionHandler;
    }

    /**
     * Returns the layout resolver handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler
     */
    public function getLayoutResolverHandler()
    {
        return $this->layoutResolverHandler;
    }

    /**
     * Begins the transaction.
     */
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    /**
     * Commits the transaction.
     */
    public function commitTransaction()
    {
        $this->connection->commit();
    }

    /**
     * Rollbacks the transaction.
     */
    public function rollbackTransaction()
    {
        $this->connection->rollBack();
    }
}
