<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine;

use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Handler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler;
use Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;

trait TestCase
{
    use DatabaseTrait;

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
            $this->createCollectionHandler(),
            $this->createLayoutResolverHandler()
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
        $queryHelper = new QueryHelper($this->databaseConnection);

        return new LayoutHandler(
            new LayoutQueryHandler(
                $connectionHelper,
                $queryHelper
            ),
            new BlockQueryHandler(
                $connectionHelper,
                $queryHelper
            ),
            $this->createBlockHandler(),
            $this->createCollectionHandler(),
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
        $connectionHelper = new ConnectionHelper($this->databaseConnection);

        return new BlockHandler(
            new BlockQueryHandler(
                $connectionHelper,
                new QueryHelper($this->databaseConnection)
            ),
            $this->createCollectionHandler(),
            new BlockMapper(),
            new PositionHelper($this->databaseConnection)
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
            new CollectionQueryHandler(
                $connectionHelper,
                new QueryHelper($this->databaseConnection)
            ),
            new CollectionMapper(),
            new PositionHelper($this->databaseConnection)
        );
    }

    /**
     * Returns the layout resolver handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler
     */
    protected function createLayoutResolverHandler()
    {
        $connectionHelper = new ConnectionHelper($this->databaseConnection);

        return new LayoutResolverHandler(
            new LayoutResolverQueryHandler(
                $connectionHelper,
                new QueryHelper($this->databaseConnection),
                array(
                    'route' => new TargetHandler\Route(),
                    'route_prefix' => new TargetHandler\RoutePrefix(),
                    'path_info' => new TargetHandler\PathInfo(),
                    'path_info_prefix' => new TargetHandler\PathInfoPrefix(),
                    'request_uri' => new TargetHandler\RequestUri(),
                    'request_uri_prefix' => new TargetHandler\RequestUriPrefix(),
                )
            ),
            new LayoutResolverMapper()
        );
    }
}
