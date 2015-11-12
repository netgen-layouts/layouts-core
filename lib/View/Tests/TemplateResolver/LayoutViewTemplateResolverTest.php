<?php

namespace Netgen\BlockManager\View\Tests\TemplateResolver;

use Netgen\BlockManager\View\TemplateResolver\LayoutViewTemplateResolver;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\Tests\Stubs\View;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\View\LayoutView;
use PHPUnit_Framework_TestCase;

class LayoutViewTemplateResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\LayoutViewTemplateResolver::resolveTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testResolveTemplateThrowsInvalidArgumentExceptionIfNoLayoutIdentifier()
    {
        $layoutViewTemplateResolver = new LayoutViewTemplateResolver();
        $layoutViewTemplateResolver->resolveTemplate($this->getLayoutView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\LayoutViewTemplateResolver::resolveTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testResolveTemplateThrowsInvalidArgumentExceptionIfNoContext()
    {
        $layoutViewTemplateResolver = new LayoutViewTemplateResolver(
            array(
                '3_zones_a' => array(),
            )
        );

        $layoutViewTemplateResolver->resolveTemplate($this->getLayoutView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\LayoutViewTemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver\LayoutViewTemplateResolver::resolveTemplate
     */
    public function testResolveTemplate()
    {
        $layoutViewTemplateResolver = new LayoutViewTemplateResolver(
            array(
                '3_zones_a' => array(
                    'templates' => array(
                        'api' => 'some_template.html.twig',
                    ),
                ),
            )
        );

        $template = $layoutViewTemplateResolver->resolveTemplate($this->getLayoutView());
        self::assertEquals('some_template.html.twig', $template);
    }

    /**
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\TemplateResolver\LayoutViewTemplateResolver::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($view, $supports)
    {
        $layoutViewTemplateResolver = new LayoutViewTemplateResolver();
        self::assertEquals($supports, $layoutViewTemplateResolver->supports($view));
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
            array(new BlockView(), false),
            array(new LayoutView(), true),
        );
    }

    /**
     * Returns the layout view used for testing.
     *
     * @return \Netgen\BlockManager\View\LayoutView
     */
    protected function getLayoutView()
    {
        $layout = new Layout(
            array(
                'identifier' => '3_zones_a',
            )
        );

        $layoutView = new LayoutView();
        $layoutView->setLayout($layout);
        $layoutView->setContext('api');

        return $layoutView;
    }
}
