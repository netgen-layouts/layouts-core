<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine;

use Netgen\BlockManager\Core\Persistence\Doctrine\Connection\Helper;
use Netgen\BlockManager\Core\Persistence\Doctrine\Handler;
use Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler as LayoutHandler;
use Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper as LayoutMapper;
use Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler as BlockHandler;
use Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper as BlockMapper;
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
            $this->createBlockHandler(),
            $this->createLayoutHandler()
        );
    }

    /**
     * Returns the layout handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Layout
     */
    protected function createLayoutHandler()
    {
        return new LayoutHandler(
            $this->databaseConnection,
            new Helper($this->databaseConnection),
            new LayoutMapper()
        );
    }

    /**
     * Returns the block handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Block
     */
    protected function createBlockHandler()
    {
        return new BlockHandler(
            $this->databaseConnection,
            new Helper($this->databaseConnection),
            new BlockMapper()
        );
    }
}
