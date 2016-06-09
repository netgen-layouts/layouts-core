<?php

namespace Netgen\BlockManager\Tests\Configuration\Registry;

use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Configuration\Registry\SourceRegistry;

class SourceRegistryTest extends \PHPUnit\Framework\TestCase
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

        $this->source = new Source(
            'source',
            true,
            'Source',
            array()
        );

        $this->registry->addSource($this->source);
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::addSource
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::getSources
     */
    public function testAddSource()
    {
        self::assertEquals(array('source' => $this->source), $this->registry->getSources());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::hasSource
     */
    public function testHasSource()
    {
        self::assertTrue($this->registry->hasSource('source'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::hasSource
     */
    public function testHasSourceWithNoSource()
    {
        self::assertFalse($this->registry->hasSource('other_source'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::getSource
     */
    public function testGetSource()
    {
        self::assertEquals($this->source, $this->registry->getSource('source'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\SourceRegistry::getSource
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testGetSourceThrowsNotFoundException()
    {
        $this->registry->getSource('other_source');
    }
}
