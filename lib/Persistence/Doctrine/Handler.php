<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\BlockManager\Persistence\HandlerInterface;

final class Handler implements HandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface
     */
    private $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
     */
    private $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface
     */
    private $layoutResolverHandler;

    public function __construct(
        Connection $connection,
        LayoutHandlerInterface $layoutHandler,
        BlockHandlerInterface $blockHandler,
        CollectionHandlerInterface $collectionHandler,
        LayoutResolverHandlerInterface $layoutResolverHandler
    ) {
        $this->connection = $connection;
        $this->layoutHandler = $layoutHandler;
        $this->blockHandler = $blockHandler;
        $this->collectionHandler = $collectionHandler;
        $this->layoutResolverHandler = $layoutResolverHandler;
    }

    public function getLayoutHandler(): LayoutHandlerInterface
    {
        return $this->layoutHandler;
    }

    public function getBlockHandler(): BlockHandlerInterface
    {
        return $this->blockHandler;
    }

    public function getCollectionHandler(): CollectionHandlerInterface
    {
        return $this->collectionHandler;
    }

    public function getLayoutResolverHandler(): LayoutResolverHandlerInterface
    {
        return $this->layoutResolverHandler;
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commitTransaction(): void
    {
        $this->connection->commit();
    }

    public function rollbackTransaction(): void
    {
        $this->connection->rollBack();
    }
}
