<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\TargetBuilder;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\Registry;
use Netgen\BlockManager\Tests\LayoutResolver\Stubs\TargetBuilder;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Registry::addTargetBuilder
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Registry::getTargetBuilders
     */
    public function testAddTargetBuilder()
    {
        $registry = new Registry();

        $targetBuilder = new TargetBuilder();
        $registry->addTargetBuilder($targetBuilder);

        self::assertEquals(array('target' => $targetBuilder), $registry->getTargetBuilders());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Registry::getTargetBuilder
     */
    public function testGetTargetBuilder()
    {
        $registry = new Registry();

        $targetBuilder = new TargetBuilder();
        $registry->addTargetBuilder($targetBuilder);

        self::assertEquals($targetBuilder, $registry->getTargetBuilder('target'));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Registry::getTargetBuilder
     * @expectedException \InvalidArgumentException
     */
    public function testGetTargetBuilderThrowsInvalidArgumentException()
    {
        $registry = new Registry();

        $targetBuilder = new TargetBuilder();
        $registry->addTargetBuilder($targetBuilder);

        $registry->getTargetBuilder('other_target');
    }
}
