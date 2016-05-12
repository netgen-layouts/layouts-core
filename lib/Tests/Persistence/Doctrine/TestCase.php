<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine;

use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Handler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler;
use Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Tests\DoctrineDatabaseTrait;

trait TestCase
{
    use DoctrineDatabaseTrait;

    /**
     * Sets up the database connection.
     */
    public function prepareHandlers()
    {
        $this->prepareDatabase(
            __DIR__ . '/../../_fixtures/schema',
            __DIR__ . '/../../_fixtures'
        );
    }

    /**
     * Returns the persistence handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Handler
     */
    protected function createPersistenceHandler()
    {
        return new Handler(
            $this->databaseConnection,
            $this->createLayoutHandler(),
            $this->createBlockHandler(),
            $this->createCollectionHandler()
        );
    }

    /**
     * Returns the layout handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\LayoutHandler
     */
    protected function createLayoutHandler()
    {
        $connectionHelper = new ConnectionHelper($this->databaseConnection);

        return new LayoutHandler(
            $this->createBlockHandler(),
            $this->createCollectionHandler(),
            new LayoutMapper(),
            $connectionHelper,
            new QueryHelper($this->databaseConnection, $connectionHelper)
        );
    }

    /**
     * Returns the block handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\BlockHandler
     */
    protected function createBlockHandler()
    {
        $connectionHelper = new ConnectionHelper($this->databaseConnection);

        return new BlockHandler(
            $this->createCollectionHandler(),
            new BlockMapper(),
            $connectionHelper,
            new PositionHelper($this->databaseConnection),
            new QueryHelper($this->databaseConnection, $connectionHelper)
        );
    }

    /**
     * Returns the collection handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected function createCollectionHandler()
    {
        $connectionHelper = new ConnectionHelper($this->databaseConnection);

        return new CollectionHandler(
            new CollectionMapper(),
            $connectionHelper,
            new PositionHelper($this->databaseConnection),
            new QueryHelper($this->databaseConnection, $connectionHelper)
        );
    }
}
