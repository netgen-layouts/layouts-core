<?php

namespace Netgen\BlockManager\Tests\Collection\Registry;

use Netgen\BlockManager\Tests\Collection\Stubs\ValueLoader;
use Netgen\BlockManager\Collection\Registry\ValueLoaderRegistry;

class ValueLoaderRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\ValueLoader\ValueLoaderInterface
     */
    protected $valueLoader;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ValueLoaderRegistry();

        $this->valueLoader = new ValueLoader();
        $this->registry->addValueLoader($this->valueLoader);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistry::addValueLoader
     * @covers \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistry::getValueLoaders
     */
    public function testAddValueLoader()
    {
        self::assertEquals(array('value' => $this->valueLoader), $this->registry->getValueLoaders());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistry::getValueLoader
     */
    public function testGetValueLoader()
    {
        self::assertEquals($this->valueLoader, $this->registry->getValueLoader('value'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistry::getValueLoader
     * @expectedException \InvalidArgumentException
     */
    public function testGetValueLoaderThrowsInvalidArgumentException()
    {
        $this->registry->getValueLoader('other_value');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistry::hasValueLoader
     */
    public function testHasValueLoader()
    {
        self::assertTrue($this->registry->hasValueLoader('value'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistry::hasValueLoader
     */
    public function testHasValueLoaderWithNoValueLoader()
    {
        self::assertFalse($this->registry->hasValueLoader('other_value'));
    }
}
