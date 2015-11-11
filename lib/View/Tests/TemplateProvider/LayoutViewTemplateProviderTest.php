<?php

namespace Netgen\BlockManager\View\Tests\TemplateProvider;

use Netgen\BlockManager\View\TemplateProvider\LayoutViewTemplateProvider;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\Tests\Stubs\View;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\View\LayoutView;
use PHPUnit_Framework_TestCase;

class LayoutViewTemplateProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\TemplateProvider\LayoutViewTemplateProvider::provideTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testProvideTemplateThrowsInvalidArgumentExceptionIfNoLayoutIdentifier()
    {
        $layoutViewTemplateProvider = new LayoutViewTemplateProvider();
        $layoutViewTemplateProvider->provideTemplate($this->getLayoutView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateProvider\LayoutViewTemplateProvider::provideTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testProvideTemplateThrowsInvalidArgumentExceptionIfNoContext()
    {
        $layoutViewTemplateProvider = new LayoutViewTemplateProvider(
            array(
                '3_zones_a' => array(),
            )
        );

        $layoutViewTemplateProvider->provideTemplate($this->getLayoutView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateProvider\LayoutViewTemplateProvider::__construct
     * @covers \Netgen\BlockManager\View\TemplateProvider\LayoutViewTemplateProvider::provideTemplate
     */
    public function testProvideTemplate()
    {
        $layoutViewTemplateProvider = new LayoutViewTemplateProvider(
            array(
                '3_zones_a' => array(
                    'templates' => array(
                        'api' => 'some_template.html.twig',
                    ),
                ),
            )
        );

        $template = $layoutViewTemplateProvider->provideTemplate($this->getLayoutView());
        self::assertEquals('some_template.html.twig', $template);
    }

    /**
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\TemplateProvider\LayoutViewTemplateProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($view, $supports)
    {
        $layoutViewTemplateProvider = new LayoutViewTemplateProvider();
        self::assertEquals($supports, $layoutViewTemplateProvider->supports($view));
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
