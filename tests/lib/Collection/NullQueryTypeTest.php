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
        $this->queryType = new NullQueryType();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::getType
     */
    public function testGetType()
    {
        $this->assertEquals('null', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Invalid query type', $this->queryType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::getForms
     */
    public function testGetForms()
    {
        $this->assertEquals([], $this->queryType->getForms());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::hasForm
     */
    public function testHasForm()
    {
        $this->assertFalse($this->queryType->hasForm('full'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\NullQueryType::getForm
     */
    public function testGetForm()
    {
        $this->assertNull($this->queryType->getForm('full'));
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
