<?php

namespace Netgen\BlockManager\View\Tests\TemplateProvider;

use Netgen\BlockManager\View\TemplateProvider\BlockViewTemplateProvider;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\Tests\Stubs\View;
use Netgen\BlockManager\View\BlockView;
use PHPUnit_Framework_TestCase;

class BlockViewTemplateProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\TemplateProvider\BlockViewTemplateProvider::provideTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testProvideTemplateThrowsInvalidArgumentExceptionIfNotBlockView()
    {
        $blockViewTemplateProvider = new BlockViewTemplateProvider();
        $blockViewTemplateProvider->provideTemplate(new View());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateProvider\BlockViewTemplateProvider::provideTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testProvideTemplateThrowsInvalidArgumentExceptionIfNoBlockDefinition()
    {
        $blockViewTemplateProvider = new BlockViewTemplateProvider();
        $blockViewTemplateProvider->provideTemplate($this->getBlockView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateProvider\BlockViewTemplateProvider::provideTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testProvideTemplateThrowsInvalidArgumentExceptionIfNoViewType()
    {
        $blockViewTemplateProvider = new BlockViewTemplateProvider(
            array(
                'paragraph' => array(),
            )
        );

        $blockViewTemplateProvider->provideTemplate($this->getBlockView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateProvider\BlockViewTemplateProvider::provideTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testProvideTemplateThrowsInvalidArgumentExceptionIfNoContext()
    {
        $blockViewTemplateProvider = new BlockViewTemplateProvider(
            array(
                'paragraph' => array(
                    'templates' => array(
                        'default' => array(),
                    ),
                ),
            )
        );

        $blockViewTemplateProvider->provideTemplate($this->getBlockView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateProvider\BlockViewTemplateProvider::__construct
     * @covers \Netgen\BlockManager\View\TemplateProvider\BlockViewTemplateProvider::provideTemplate
     */
    public function testProvideTemplate()
    {
        $blockViewTemplateProvider = new BlockViewTemplateProvider(
            array(
                'paragraph' => array(
                    'templates' => array(
                        'default' => array(
                            'api' => 'some_template.html.twig',
                        ),
                    ),
                ),
            )
        );

        $template = $blockViewTemplateProvider->provideTemplate($this->getBlockView());
        self::assertEquals('some_template.html.twig', $template);
    }

    /**
     * Returns the block view used for testing.
     *
     * @return \Netgen\BlockManager\View\BlockView
     */
    protected function getBlockView()
    {
        $block = new Block(
            array(
                'definitionIdentifier' => 'paragraph',
                'viewType' => 'default',
            )
        );

        $blockView = new BlockView();
        $blockView->setBlock($block);
        $blockView->setContext('api');

        return $blockView;
    }
}
