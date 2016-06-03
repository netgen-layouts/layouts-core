<?php

namespace Netgen\BlockManager\Tests\Configuration\Source;

use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Configuration\Source\Query;

class SourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\Source\Source
     */
    protected $source;

    public function setUp()
    {
        $this->source = new Source(
            'source',
            true,
            'Source',
            array(
                'default' => new Query(
                    'default',
                    'ezcontent_search',
                    array('parent_location_id' => 2)
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
        self::assertEquals('source', $this->source->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::isEnabled
     */
    public function testGetIsEnabled()
    {
        self::assertEquals(true, $this->source->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::getName
     */
    public function testGetName()
    {
        self::assertEquals('Source', $this->source->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::getQueries
     */
    public function testGetQueries()
    {
        self::assertEquals(
            array(
                'default' => new Query(
                    'default',
                    'ezcontent_search',
                    array('parent_location_id' => 2)
                ),
            ),
            $this->source->getQueries()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::hasQuery
     */
    public function testHasQuery()
    {
        self::assertTrue($this->source->hasQuery('default'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::hasQuery
     */
    public function testHasQueryReturnsFalse()
    {
        self::assertFalse($this->source->hasQuery('other'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Source::getQuery
     */
    public function testGetQuery()
    {
        self::assertEquals(
            new Query(
                'default',
                'ezcontent_search',
                array('parent_location_id' => 2)
            ),
            $this->source->getQuery('default')
        );
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
