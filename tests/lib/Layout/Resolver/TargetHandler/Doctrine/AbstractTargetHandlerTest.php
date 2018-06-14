<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\TestCase;

abstract class AbstractTargetHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler
     */
    protected $handler;

    public function setUp(): void
    {
        $this->createDatabase();

        $this->handler = new LayoutResolverHandler(
            new LayoutResolverQueryHandler(
                $this->databaseConnection,
                new ConnectionHelper($this->databaseConnection),
                [$this->getTargetIdentifier() => $this->getTargetHandler()]
            ),
            new LayoutResolverMapper()
        );
    }

    /**
     * Tears down the tests.
     */
    public function tearDown(): void
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
