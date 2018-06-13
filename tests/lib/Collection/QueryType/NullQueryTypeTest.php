<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\QueryType;

use Netgen\BlockManager\Collection\QueryType\NullQueryType;
use Netgen\BlockManager\Core\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

final class NullQueryTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType\NullQueryType
     */
    private $queryType;

    public function setUp()
    {
        $this->queryType = new NullQueryType('type');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\NullQueryType::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType\NullQueryType::getType
     */
    public function testGetType()
    {
        $this->assertEquals('type', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\NullQueryType::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->queryType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\NullQueryType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Invalid query type', $this->queryType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\NullQueryType::getValues
     */
    public function testGetValues()
    {
        $this->assertEquals([], $this->queryType->getValues(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\NullQueryType::getCount
     */
    public function testGetCount()
    {
        $this->assertEquals(0, $this->queryType->getCount(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\NullQueryType::isContextual
     */
    public function testIsContextual()
    {
        $this->assertFalse($this->queryType->isContextual(new Query()));
    }
}
