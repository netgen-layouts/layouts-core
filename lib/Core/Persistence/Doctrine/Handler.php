<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine;

use Netgen\BlockManager\Persistence\Handler as HandlerInterface;
use Netgen\BlockManager\Persistence\Handler\Layout;
use Netgen\BlockManager\Persistence\Handler\Block;
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
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Persistence\Handler\Layout $layoutHandler
     */
    public function __construct(
        Connection $connection,
        Layout $layoutHandler,
        Block $blockHandler
    ) {
        $this->connection = $connection;
        $this->layoutHandler = $layoutHandler;
        $this->blockHandler = $blockHandler;
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
