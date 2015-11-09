<?php

namespace Netgen\BlockManager\Registry\Tests\ViewBuilderRegistry;

use Netgen\BlockManager\Registry\ViewBuilderRegistry\ArrayBased;
use Netgen\BlockManager\View\Builder\BlockViewBuilder;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use PHPUnit_Framework_TestCase;

class ArrayBasedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Registry\ViewBuilderRegistry\ArrayBased::addViewBuilder
     * @covers \Netgen\BlockManager\Registry\ViewBuilderRegistry\ArrayBased::getViewBuilders
     */
    public function testAddViewBuilder()
    {
        $registry = new ArrayBased();

        $blockViewBuilder = new BlockViewBuilder();
        $registry->addViewBuilder(
            $blockViewBuilder,
            'Netgen\BlockManager\Core\Values\Page\Block'
        );

        self::assertEquals(
            array(
                'Netgen\BlockManager\Core\Values\Page\Block' => $blockViewBuilder,
            ),
            $registry->getViewBuilders()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Registry\ViewBuilderRegistry\ArrayBased::getViewBuilder
     */
    public function testGetViewBuilder()
    {
        $registry = new ArrayBased();

        $blockViewBuilder = new BlockViewBuilder();
        $registry->addViewBuilder(
            $blockViewBuilder,
            'Netgen\BlockManager\Core\Values\Page\Block'
        );

        self::assertEquals($blockViewBuilder, $registry->getViewBuilder(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Registry\ViewBuilderRegistry\ArrayBased::getViewBuilder
     * @expectedException \InvalidArgumentException
     */
    public function testGetViewBuilderInvalidArgumentException()
    {
        $registry = new ArrayBased();

        $blockViewBuilder = new BlockViewBuilder();
        $registry->addViewBuilder(
            $blockViewBuilder,
            'Netgen\BlockManager\Core\Values\Page\Block'
        );

        $registry->getViewBuilder(new Layout());
    }
}
