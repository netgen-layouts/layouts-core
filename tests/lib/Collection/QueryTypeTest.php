<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Collection\QueryType\Configuration\Form;
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
                'handler' => new QueryTypeHandler(array('val1', 'val2')),
                'type' => 'query_type',
                'name' => 'Query type',
                'forms' => array(
                    'full' => new Form(
                        array(
                            'identifier' => 'full',
                            'type' => 'form_type',
                        )
                    ),
                ),
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
     * @covers \Netgen\BlockManager\Collection\QueryType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Query type', $this->queryType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getForms
     */
    public function testGetForms()
    {
        $this->assertEquals(
            array(
                'full' => new Form(array('identifier' => 'full', 'type' => 'form_type')),
            ),
            $this->queryType->getForms()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::hasForm
     */
    public function testHasForm()
    {
        $this->assertTrue($this->queryType->hasForm('full'));
        $this->assertFalse($this->queryType->hasForm('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getForm
     */
    public function testGetForm()
    {
        $this->assertEquals(
            new Form(array('identifier' => 'full', 'type' => 'form_type')),
            $this->queryType->getForm('full')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getForm
     * @expectedException \Netgen\BlockManager\Exception\Collection\QueryTypeException
     * @expectedExceptionMessage Form "unknown" does not exist in "query_type" query type.
     */
    public function testGetFormThrowsQueryTypeException()
    {
        $this->queryType->getForm('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getValues
     */
    public function testGetValues()
    {
        $this->assertEquals(array('val1', 'val2'), $this->queryType->getValues(new Query()));
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
