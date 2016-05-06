<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\TargetBuilder;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\Registry;
use Netgen\BlockManager\Tests\LayoutResolver\Stubs\TargetBuilder;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface
     */
    protected $targetBuilder;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\TargetBuilder\RegistryInterface
     */
    protected $registry;

    public function setUp()
    {
        $this->targetBuilder = new TargetBuilder();

        $this->registry = new Registry();
        $this->registry->addTargetBuilder('target', $this->targetBuilder);
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Registry::addTargetBuilder
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Registry::getTargetBuilders
     */
    public function testAddTargetBuilder()
    {
        self::assertEquals(array('target' => $this->targetBuilder), $this->registry->getTargetBuilders());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Registry::getTargetBuilder
     */
    public function testGetTargetBuilder()
    {
        self::assertEquals($this->targetBuilder, $this->registry->getTargetBuilder('target'));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Registry::getTargetBuilder
     * @expectedException \InvalidArgumentException
     */
    public function testGetTargetBuilderThrowsInvalidArgumentException()
    {
        $this->registry->getTargetBuilder('other_target');
    }
}
