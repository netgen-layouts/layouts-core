<?php

namespace Netgen\BlockManager\View\Tests\TemplateResolver;

use Netgen\BlockManager\View\TemplateResolver\BlockViewTemplateResolver;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\Tests\Stubs\View;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\View\LayoutView;
use PHPUnit_Framework_TestCase;

class BlockViewTemplateResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\BlockViewTemplateResolver::resolveTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testResolveTemplateThrowsInvalidArgumentExceptionIfNoBlockDefinition()
    {
        $blockViewTemplateResolver = new BlockViewTemplateResolver();
        $blockViewTemplateResolver->resolveTemplate($this->getBlockView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\BlockViewTemplateResolver::resolveTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testResolveTemplateThrowsInvalidArgumentExceptionIfNoViewType()
    {
        $blockViewTemplateResolver = new BlockViewTemplateResolver(
            array(
                'paragraph' => array(),
            )
        );

        $blockViewTemplateResolver->resolveTemplate($this->getBlockView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\BlockViewTemplateResolver::resolveTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testResolveTemplateThrowsInvalidArgumentExceptionIfNoContext()
    {
        $blockViewTemplateResolver = new BlockViewTemplateResolver(
            array(
                'paragraph' => array(
                    'default' => array(),
                ),
            )
        );

        $blockViewTemplateResolver->resolveTemplate($this->getBlockView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\BlockViewTemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver\BlockViewTemplateResolver::resolveTemplate
     */
    public function testResolveTemplate()
    {
        $blockViewTemplateResolver = new BlockViewTemplateResolver(
            array(
                'paragraph' => array(
                    'default' => array(
                        'api' => 'some_template.html.twig',
                    ),
                ),
            )
        );

        $template = $blockViewTemplateResolver->resolveTemplate($this->getBlockView());
        self::assertEquals('some_template.html.twig', $template);
    }

    /**
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\TemplateResolver\BlockViewTemplateResolver::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($view, $supports)
    {
        $blockViewProvider = new BlockViewTemplateResolver();
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
