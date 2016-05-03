<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine;

use Netgen\BlockManager\Core\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Core\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Core\Persistence\Doctrine\Handler;
use Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler;
use Netgen\BlockManager\Core\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Persistence\Doctrine\Handler\BlockHandler;
use Netgen\BlockManager\Core\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Persistence\Doctrine\Handler\CollectionHandler;
use Netgen\BlockManager\Core\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Tests\DoctrineDatabaseTrait;

trait TestCase
{
    use DoctrineDatabaseTrait;

    /**
     * Sets up the database connection.
     */
    public function prepareHandlers()
    {
        $this->prepareDatabase(__DIR__ . '/_fixtures/schema', __DIR__ . '/_fixtures');
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
        return new LayoutHandler(
            $this->databaseConnection,
            new ConnectionHelper($this->databaseConnection),
            $this->createBlockHandler(),
            new LayoutMapper()
        );
    }

    /**
     * Returns the block handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\BlockHandler
     */
    protected function createBlockHandler()
    {
        return new BlockHandler(
            $this->databaseConnection,
            new ConnectionHelper($this->databaseConnection),
            new PositionHelper($this->databaseConnection),
            new BlockMapper()
        );
    }

    /**
     * Returns the collection handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected function createCollectionHandler()
    {
        return new CollectionHandler(
            $this->databaseConnection,
            new ConnectionHelper($this->databaseConnection),
            new CollectionMapper()
        );
    }
}
