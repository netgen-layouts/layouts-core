<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine;

use Netgen\BlockManager\Persistence\Handler as HandlerInterface;
use Netgen\BlockManager\Persistence\Handler\Layout;
use Netgen\BlockManager\Persistence\Handler\Block;
use Netgen\BlockManager\Persistence\Handler\Collection;
use Doctrine\DBAL\Connection;

class Handler implements HandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\Layout
     */
    protected $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\Block
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\Collection
     */
    protected $collectionHandler;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Persistence\Handler\Layout $layoutHandler
     * @param \Netgen\BlockManager\Persistence\Handler\Block $blockHandler
     * @param \Netgen\BlockManager\Persistence\Handler\Collection $collectionHandler
     */
    public function __construct(
        Connection $connection,
        Layout $layoutHandler,
        Block $blockHandler,
        Collection $collectionHandler
    ) {
        $this->connection = $connection;
        $this->layoutHandler = $layoutHandler;
        $this->blockHandler = $blockHandler;
        $this->collectionHandler = $collectionHandler;
    }

    /**
     * Returns the layout handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Layout
     */
    public function getLayoutHandler()
    {
        return $this->layoutHandler;
    }

    /**
     * Returns the block handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Block
     */
    public function getBlockHandler()
    {
        return $this->blockHandler;
    }

    /**
     * Returns the collection handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Collection
     */
    public function getCollectionHandler()
    {
        return $this->collectionHandler;
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
