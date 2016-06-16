<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler;
use PHPUnit\Framework\TestCase;

class QueryTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    protected $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType
     */
    protected $queryType;

    public function setUp()
    {
        $this->handler = new QueryTypeHandler();

        $this->configMock = $this->createMock(Configuration::class);

        $this->queryType = new QueryType(
            'query_type',
            $this->handler,
            $this->configMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType::getType
     */
    public function testGetType()
    {
        self::assertEquals('query_type', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getHandler
     */
    public function testGetHandler()
    {
        self::assertEquals($this->handler, $this->queryType->getHandler());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getConfig
     */
    public function testGetConfig()
    {
        self::assertEquals($this->configMock, $this->queryType->getConfig());
    }
}
