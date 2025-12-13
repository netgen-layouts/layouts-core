<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;
use Netgen\Layouts\Tests\Stubs\Container;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Factory\UuidFactory;

abstract class TargetHandlerTestBase extends TestCase
{
    use DatabaseTrait;

    final protected LayoutResolverHandler $handler;

    final protected function setUp(): void
    {
        $this->createDatabase();

        $this->handler = new LayoutResolverHandler(
            self::createStub(LayoutHandlerInterface::class),
            new LayoutResolverQueryHandler(
                $this->databaseConnection,
                new ConnectionHelper($this->databaseConnection),
                new Container(
                    [
                        $this->getTargetIdentifier() => $this->getTargetHandler(),
                    ],
                ),
            ),
            new LayoutResolverMapper(),
            new UuidFactory(),
        );
    }

    final protected function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * Returns the target identifier under test.
     */
    abstract protected function getTargetIdentifier(): string;

    /**
     * Creates the handler under test.
     */
    abstract protected function getTargetHandler(): TargetHandlerInterface;
}
