<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\NullQueryType;
use Netgen\BlockManager\Core\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

final class NullQueryTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\NullQueryType
     */
    private $queryType;

    public function setUp()
    {
        $this->queryType = new NullQueryType('type');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::__construct
     * @covers \Netgen\BlockManager\Collection\NullQueryType::getType
     */
    public function testGetType()
    {
        $this->assertEquals('type', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->queryType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Invalid query type', $this->queryType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::getValues
     */
    public function testGetValues()
    {
        $this->assertEquals([], $this->queryType->getValues(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::getCount
     */
    public function testGetCount()
    {
        $this->assertEquals(0, $this->queryType->getCount(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::isContextual
     */
    public function testIsContextual()
    {
        $this->assertFalse($this->queryType->isContextual(new Query()));
    }
}
