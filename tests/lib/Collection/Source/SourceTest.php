<?php

namespace Netgen\BlockManager\Tests\Collection\Source;

use Netgen\BlockManager\Collection\Source\Query;
use Netgen\BlockManager\Collection\Source\Source;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class SourceTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\Source\Query
     */
    protected $query;

    /**
     * @var \Netgen\BlockManager\Collection\Source\Source
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
     * @covers \Netgen\BlockManager\Collection\Source\Source::__construct
     * @covers \Netgen\BlockManager\Collection\Source\Source::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('source', $this->source->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Source\Source::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Source', $this->source->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Source\Source::getQueries
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
     * @covers \Netgen\BlockManager\Collection\Source\Source::hasQuery
     */
    public function testHasQuery()
    {
        $this->assertTrue($this->source->hasQuery('default'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Source\Source::hasQuery
     */
    public function testHasQueryReturnsFalse()
    {
        $this->assertFalse($this->source->hasQuery('other'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Source\Source::getQuery
     */
    public function testGetQuery()
    {
        $this->assertEquals($this->query, $this->source->getQuery('default'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Source\Source::getQuery
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Query with "other" identifier does not exist in "source" source.
     */
    public function testGetQueryThrowsInvalidArgumentException()
    {
        $this->source->getQuery('other');
    }
}
