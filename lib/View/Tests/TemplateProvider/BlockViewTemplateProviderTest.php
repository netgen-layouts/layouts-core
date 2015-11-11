<?php

namespace Netgen\BlockManager\View\Tests\TemplateProvider;

use Netgen\BlockManager\View\TemplateProvider\BlockViewTemplateProvider;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\Tests\Stubs\View;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\View\LayoutView;
use PHPUnit_Framework_TestCase;

class BlockViewTemplateProviderTest extends PHPUnit_Framework_TestCase
{
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
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\TemplateProvider\BlockViewTemplateProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($view, $supports)
    {
        $blockViewProvider = new BlockViewTemplateProvider();
        self::assertEquals($supports, $blockViewProvider->supports($view));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new View(), false),
            array(new BlockView(), true),
            array(new LayoutView(), false),
        );
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
