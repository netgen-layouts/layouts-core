<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Netgen\BlockManager\Layout\Resolver\TargetHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\BlockManager\Persistence\HandlerInterface;

trait TestCaseTrait
{
    use DatabaseTrait;

    /**
     * Returns the persistence handler under test.
     */
    private function createPersistenceHandler(Connection $connection = null): HandlerInterface
    {
        $this->databaseConnection = $connection ?? $this->createDatabaseConnection();

        if ($connection === null) {
            $this->createDatabase();
        }

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
     */
    private function createLayoutHandler(): LayoutHandlerInterface
    {
        $connectionHelper = new ConnectionHelper($this->databaseConnection);

        return new LayoutHandler(
            new LayoutQueryHandler(
                $this->databaseConnection,
                $connectionHelper
            ),
            $this->createBlockHandler(),
            new LayoutMapper()
        );
    }

    /**
     * Returns the block handler under test.
     */
    private function createBlockHandler(): BlockHandlerInterface
    {
        return new BlockHandler(
            new BlockQueryHandler(
                $this->databaseConnection,
                new ConnectionHelper($this->databaseConnection)
            ),
            $this->createCollectionHandler(),
            new BlockMapper(),
            new PositionHelper($this->databaseConnection)
        );
    }

    /**
     * Returns the collection handler under test.
     */
    private function createCollectionHandler(): CollectionHandlerInterface
    {
        return new CollectionHandler(
            new CollectionQueryHandler(
                $this->databaseConnection,
                new ConnectionHelper($this->databaseConnection)
            ),
            new CollectionMapper(),
            new PositionHelper($this->databaseConnection)
        );
    }

    /**
     * Returns the layout resolver handler under test.
     */
    private function createLayoutResolverHandler(): LayoutResolverHandlerInterface
    {
        return new LayoutResolverHandler(
            new LayoutResolverQueryHandler(
                $this->databaseConnection,
                new ConnectionHelper($this->databaseConnection),
                [
                    'route' => new TargetHandler\Doctrine\Route(),
                    'route_prefix' => new TargetHandler\Doctrine\RoutePrefix(),
                    'path_info' => new TargetHandler\Doctrine\Route(),
                    'path_info_prefix' => new TargetHandler\Doctrine\RoutePrefix(),
                    'request_uri' => new TargetHandler\Doctrine\Route(),
                    'request_uri_prefix' => new TargetHandler\Doctrine\RoutePrefix(),
                ]
            ),
            new LayoutResolverMapper()
        );
    }
}
