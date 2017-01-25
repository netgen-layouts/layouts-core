<?php

namespace Netgen\BlockManager\Tests\Configuration\Registry;

use Netgen\BlockManager\Configuration\Registry\SourceRegistry;
use Netgen\BlockManager\Configuration\Source\Source;
use PHPUnit\Framework\TestCase;

class SourceRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\Source\Source
     */
    protected $source;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\SourceRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new SourceRegistry();

        $this->source = new Source(array('identifier' => 'source'));

        $this->registry->addSource($this->source);
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::addSource
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::getSources
     */
    public function testAddSource()
    {
        $this->assertEquals(array('source' => $this->source), $this->registry->getSources());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::hasSource
     */
    public function testHasSource()
    {
        $this->assertTrue($this->registry->hasSource('source'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::hasSource
     */
    public function testHasSourceWithNoSource()
    {
        $this->assertFalse($this->registry->hasSource('other_source'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::getSource
     */
    public function testGetSource()
    {
        $this->assertEquals($this->source, $this->registry->getSource('source'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::getSource
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetSourceThrowsInvalidArgumentException()
    {
        $this->registry->getSource('other_source');
    }
}