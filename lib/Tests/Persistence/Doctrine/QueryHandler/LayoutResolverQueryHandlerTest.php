<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use PHPUnit\Framework\TestCase;
use stdClass;

class LayoutResolverQueryHandlerTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::__construct
     * @expectedException \RuntimeException
     */
    public function testConstructorThrowsRuntimeException()
    {
        $connectionMock = $this->createMock(Connection::class);
        $queryHandler = new LayoutResolverQueryHandler(
            $connectionMock,
            new ConnectionHelper($connectionMock),
            array(new stdClass())
        );
    }
}
