<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine;

use Netgen\BlockManager\Persistence\Handler as HandlerInterface;
use Netgen\BlockManager\Persistence\Handler\Block;
use Netgen\BlockManager\Persistence\Handler\Layout;
use Doctrine\DBAL\Connection;

class Handler implements HandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\Block
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\Layout
     */
    protected $layoutHandler;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Persistence\Handler\Block $blockHandler
     * @param \Netgen\BlockManager\Persistence\Handler\Layout $layoutHandler
     */
    public function __construct(Connection $connection, Block $blockHandler, Layout $layoutHandler)
    {
        $this->connection = $connection;
        $this->blockHandler = $blockHandler;
        $this->layoutHandler = $layoutHandler;
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
     * Returns the layout handler.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Layout
     */
    public function getLayoutHandler()
    {
        return $this->layoutHandler;
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
