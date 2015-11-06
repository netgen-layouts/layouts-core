<?php

namespace Netgen\BlockManager\View\Tests\Builder;

use Netgen\BlockManager\View\TemplateProvider\LayoutViewTemplateProvider;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\Tests\Stubs\View;
use Netgen\BlockManager\View\LayoutView;
use PHPUnit_Framework_TestCase;

class LayoutViewTemplateProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\Builder\LayoutViewTemplateProvider::provideTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testBuildViewThrowsInvalidArgumentExceptionIfNotLayoutView()
    {
        $layoutViewBuilder = new LayoutViewTemplateProvider();
        $layoutViewBuilder->provideTemplate(new View());
    }

    /**
     * @covers \Netgen\BlockManager\View\Builder\LayoutViewTemplateProvider::provideTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testBuildViewThrowsInvalidArgumentExceptionIfNoLayoutIdentifier()
    {
        $layoutViewBuilder = new LayoutViewTemplateProvider();
        $layoutViewBuilder->provideTemplate($this->getLayoutView());
    }

    /**
     * @covers \Netgen\BlockManager\View\Builder\LayoutViewTemplateProvider::provideTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testBuildViewThrowsInvalidArgumentExceptionIfNoContext()
    {
        $layoutViewBuilder = new LayoutViewTemplateProvider(
            array(
                '3_zones_a' => array()
            )
        );

        $layoutViewBuilder->provideTemplate($this->getLayoutView());
    }

    /**
     * @covers \Netgen\BlockManager\View\Builder\LayoutViewTemplateProvider::provideTemplate
     */
    public function testBuildView()
    {
        $layoutViewBuilder = new LayoutViewTemplateProvider(
            array(
                '3_zones_a' => array(
                    'templates' => array(
                        'manager' => 'some_template.html.twig'
                    )
                )
            )
        );

        $template = $layoutViewBuilder->provideTemplate($this->getLayoutView());
        self::assertEquals('some_template.html.twig', $template);
    }

    /**
     * Returns the layout view used for testing
     *
     * @return \Netgen\BlockManager\View\LayoutView
     */
    protected function getLayoutView()
    {
        $layout = new Layout(
            array(
                'identifier' => '3_zones_a'
            )
        );

        $layoutView = new LayoutView();
        $layoutView->setLayout($layout);
        $layoutView->setContext('manager');

        return $layoutView;
    }
}
