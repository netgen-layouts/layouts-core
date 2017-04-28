<?php

namespace Netgen\BlockManager\Tests\Collection\Registry;

use Netgen\BlockManager\Collection\Registry\SourceRegistry;
use Netgen\BlockManager\Collection\Source\Source;
use PHPUnit\Framework\TestCase;

class SourceRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\Source\Source
     */
    protected $source;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\SourceRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new SourceRegistry();

        $this->source = new Source(array('identifier' => 'source'));

        $this->registry->addSource('source', $this->source);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\SourceRegistry::addSource
     * @covers \Netgen\BlockManager\Collection\Registry\SourceRegistry::getSources
     */
    public function testAddSource()
    {
        $this->assertEquals(array('source' => $this->source), $this->registry->getSources());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\SourceRegistry::hasSource
     */
    public function testHasSource()
    {
        $this->assertTrue($this->registry->hasSource('source'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\SourceRegistry::hasSource
     */
    public function testHasSourceWithNoSource()
    {
        $this->assertFalse($this->registry->hasSource('other_source'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\SourceRegistry::getSource
     */
    public function testGetSource()
    {
        $this->assertEquals($this->source, $this->registry->getSource('source'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\SourceRegistry::getSource
     * @expectedException \Netgen\BlockManager\Exception\Collection\SourceException
     * @expectedExceptionMessage Source with "other_source" identifier does not exist.
     */
    public function testGetSourceThrowsSourceException()
    {
        $this->registry->getSource('other_source');
    }
}
