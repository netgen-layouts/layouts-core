<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Registry;

use Netgen\BlockManager\Layout\Resolver\Registry\TargetBuilderRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetBuilder;

class TargetBuilderRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface
     */
    protected $targetBuilder;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetBuilderRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->targetBuilder = new TargetBuilder();

        $this->registry = new TargetBuilderRegistry();
        $this->registry->addTargetBuilder('target', $this->targetBuilder);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetBuilderRegistry::addTargetBuilder
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetBuilderRegistry::getTargetBuilders
     */
    public function testAddTargetBuilder()
    {
        self::assertEquals(array('target' => $this->targetBuilder), $this->registry->getTargetBuilders());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetBuilderRegistry::getTargetBuilder
     */
    public function testGetTargetBuilder()
    {
        self::assertEquals($this->targetBuilder, $this->registry->getTargetBuilder('target'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetBuilderRegistry::getTargetBuilder
     * @expectedException \RuntimeException
     */
    public function testGetTargetBuilderThrowsRuntimeException()
    {
        $this->registry->getTargetBuilder('other_target');
    }
}
