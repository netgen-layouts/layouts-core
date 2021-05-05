<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine;

use Netgen\Layouts\Layout\Resolver\TargetHandler;
use Netgen\Layouts\Persistence\Doctrine\Handler\BlockHandler;
use Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler;
use Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler;
use Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\Layouts\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\Layouts\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\Layouts\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\BlockQueryHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\Layouts\Persistence\Doctrine\TransactionHandler;
use Netgen\Layouts\Persistence\Handler\BlockHandlerInterface;
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use Netgen\Layouts\Tests\Stubs\Container;

trait TestCaseTrait
{
    use DatabaseTrait;

    /**
     * Returns the persistence handler under test.
     */
    protected function createTransactionHandler(): TransactionHandlerInterface
    {
        $this->createDatabase();

        return new TransactionHandler($this->databaseConnection);
    }

    /**
     * Returns the layout handler under test.
     */
    protected function createLayoutHandler(): LayoutHandlerInterface
    {
        $connectionHelper = new ConnectionHelper($this->databaseConnection);

        return new LayoutHandler(
            new LayoutQueryHandler(
                $this->databaseConnection,
                $connectionHelper,
            ),
            $this->createBlockHandler(),
            new LayoutMapper(),
        );
    }

    /**
     * Returns the block handler under test.
     */
    protected function createBlockHandler(): BlockHandlerInterface
    {
        return new BlockHandler(
            new BlockQueryHandler(
                $this->databaseConnection,
                new ConnectionHelper($this->databaseConnection),
            ),
            $this->createCollectionHandler(),
            new BlockMapper(),
            new PositionHelper($this->databaseConnection),
        );
    }

    /**
     * Returns the collection handler under test.
     */
    protected function createCollectionHandler(): CollectionHandlerInterface
    {
        return new CollectionHandler(
            new CollectionQueryHandler(
                $this->databaseConnection,
                new ConnectionHelper($this->databaseConnection),
            ),
            new CollectionMapper(),
            new PositionHelper($this->databaseConnection),
        );
    }

    /**
     * Returns the layout resolver handler under test.
     */
    protected function createLayoutResolverHandler(): LayoutResolverHandlerInterface
    {
        return new LayoutResolverHandler(
            $this->createLayoutHandler(),
            new LayoutResolverQueryHandler(
                $this->databaseConnection,
                new ConnectionHelper($this->databaseConnection),
                new Container(
                    [
                        'route' => new TargetHandler\Doctrine\Route(),
                        'route_prefix' => new TargetHandler\Doctrine\RoutePrefix(),
                        'path_info' => new TargetHandler\Doctrine\Route(),
                        'path_info_prefix' => new TargetHandler\Doctrine\RoutePrefix(),
                        'request_uri' => new TargetHandler\Doctrine\Route(),
                        'request_uri_prefix' => new TargetHandler\Doctrine\RoutePrefix(),
                    ],
                ),
            ),
            new LayoutResolverMapper(),
        );
    }
}
