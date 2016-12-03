<?php

namespace Netgen\BlockManager\Tests\Configuration\Source;

use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Configuration\Source\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class SourceTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\Source\Query
     */
    protected $query;

    /**
     * @var \Netgen\BlockManager\Configuration\Source\Source
     */
    protected $source;

    public function setUp()
    {
        $this->query = new Query(
            array(
                'identifier' => 'default',
                'queryType' => new QueryType('ezcontent_search'),
                'defaultParameters' => array('parent_location_id' => 2),
            )
        );

        $this->source = new Source(
            array(
                'identifier' => 'source',
                'name' => 'Source',
                'queries' => array(
                    'default' => $this->query,
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::__construct
     * @covers \Netgen\BlockManager\Configuration\Source\Source::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('source', $this->source->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Source', $this->source->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::getQueries
     */
    public function testGetQueries()
    {
        $this->assertEquals(
            array(
                'default' => $this->query,
            ),
            $this->source->getQueries()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::hasQuery
     */
    public function testHasQuery()
    {
        $this->assertTrue($this->source->hasQuery('default'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::hasQuery
     */
    public function testHasQueryReturnsFalse()
    {
        $this->assertFalse($this->source->hasQuery('other'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::getQuery
     */
    public function testGetQuery()
    {
        $this->assertEquals($this->query, $this->source->getQuery('default'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::getQuery
     * @expectedException \InvalidArgumentException
     */
    public function testGetQueryThrowsInvalidArgumentException()
    {
        $this->source->getQuery('other');
    }
}
