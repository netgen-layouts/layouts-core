<?php

namespace Netgen\BlockManager\Registry\Tests\ViewTemplateProviderRegistry;

use Netgen\BlockManager\Registry\ViewTemplateProviderRegistry\ArrayBased;
use Netgen\BlockManager\View\TemplateProvider\BlockViewTemplateProvider;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\View\BlockView;
use PHPUnit_Framework_TestCase;

class ArrayBasedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Registry\ViewTemplateProviderRegistry\ArrayBased::addViewTemplateProvider
     * @covers \Netgen\BlockManager\Registry\ViewTemplateProviderRegistry\ArrayBased::getViewTemplateProviders
     */
    public function testAddViewTemplateProvider()
    {
        $registry = new ArrayBased();

        $templateProvider = new BlockViewTemplateProvider();
        $registry->addViewTemplateProvider(
            $templateProvider,
            'Netgen\BlockManager\View\BlockView'
        );

        self::assertEquals(
            array(
                'Netgen\BlockManager\View\BlockView' => $templateProvider,
            ),
            $registry->getViewTemplateProviders()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Registry\ViewTemplateProviderRegistry\ArrayBased::getViewTemplateProvider
     */
    public function testGetViewTemplateProvider()
    {
        $registry = new ArrayBased();

        $templateProvider = new BlockViewTemplateProvider();
        $registry->addViewTemplateProvider(
            $templateProvider,
            'Netgen\BlockManager\View\BlockView'
        );

        self::assertEquals($templateProvider, $registry->getViewTemplateProvider(new BlockView()));
    }

    /**
     * @covers \Netgen\BlockManager\Registry\ViewTemplateProviderRegistry\ArrayBased::getViewTemplateProvider
     * @expectedException \InvalidArgumentException
     */
    public function testGetViewTemplateProviderThrowsInvalidArgumentException()
    {
        $registry = new ArrayBased();

        $templateProvider = new BlockViewTemplateProvider();
        $registry->addViewTemplateProvider(
            $templateProvider,
            'Netgen\BlockManager\View\BlockView'
        );

        $registry->getViewTemplateProvider(new LayoutView());
    }
}
