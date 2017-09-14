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

    public function getLayoutHandler()
    {
        return $this->layoutHandler;
    }

    public function getBlockHandler()
    {
        return $this->blockHandler;
    }

    public function getCollectionHandler()
    {
        return $this->collectionHandler;
    }

    public function getLayoutResolverHandler()
    {
        return $this->layoutResolverHandler;
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->connection->commit();
    }

    public function rollbackTransaction()
    {
        $this->connection->rollBack();
    }
}
