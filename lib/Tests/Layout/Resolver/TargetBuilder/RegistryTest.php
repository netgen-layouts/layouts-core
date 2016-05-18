<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetBuilder;

use Netgen\BlockManager\Layout\Resolver\TargetBuilder\Registry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetBuilder;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface
     */
    protected $targetBuilder;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\RegistryInterface
     */
    protected $registry;

    public function setUp()
    {
        $this->targetBuilder = new TargetBuilder();

        $this->registry = new Registry();
        $this->registry->addTargetBuilder('target', $this->targetBuilder);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Registry::addTargetBuilder
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Registry::getTargetBuilders
     */
    public function testAddTargetBuilder()
    {
        self::assertEquals(array('target' => $this->targetBuilder), $this->registry->getTargetBuilders());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Registry::getTargetBuilder
     */
    public function testGetTargetBuilder()
    {
        self::assertEquals($this->targetBuilder, $this->registry->getTargetBuilder('target'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Registry::getTargetBuilder
     * @expectedException \InvalidArgumentException
     */
    public function testGetTargetBuilderThrowsInvalidArgumentException()
    {
        $this->registry->getTargetBuilder('other_target');
    }
}
