<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler;
use PHPUnit\Framework\TestCase;

final class QueryTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType
     */
    private $queryType;

    public function setUp()
    {
        $this->queryType = new QueryType(
            array(
                'type' => 'query_type',
                'handler' => new QueryTypeHandler(array('val1', 'val2')),
                'config' => new Configuration(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType::getType
     */
    public function testGetType()
    {
        $this->assertEquals('query_type', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getValues
     */
    public function testGetValues()
    {
        $this->assertEquals(array('val1', 'val2'), $this->queryType->getValues(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getConfig
     */
    public function testGetConfig()
    {
        $this->assertEquals(new Configuration(), $this->queryType->getConfig());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getCount
     */
    public function testGetCount()
    {
        $this->assertEquals(2, $this->queryType->getCount(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::isContextual
     */
    public function testIsContextual()
    {
        $this->assertFalse($this->queryType->isContextual(new Query()));
    }
}
