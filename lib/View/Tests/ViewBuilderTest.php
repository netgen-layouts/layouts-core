<?php

namespace Netgen\BlockManager\View\Tests;

use Netgen\BlockManager\API\Tests\Stubs\Value;
use Netgen\BlockManager\View\Tests\Stubs\View;
use Netgen\BlockManager\View\ViewBuilder;
use PHPUnit_Framework_TestCase;

class ViewBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     */
    public function testBuildView()
    {
        $value = new Value();
        $view = new View();
        $viewWithTemplate = clone $view;
        $viewWithTemplate->setTemplate('some_template.html.twig');

        $viewProvider1 = $this->getMock('Netgen\BlockManager\View\Provider\ViewProvider');
        $viewProvider1
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(false));

        $viewProvider2 = $this->getMock('Netgen\BlockManager\View\Provider\ViewProvider');
        $viewProvider2
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(true));
        $viewProvider2
            ->expects($this->once())
            ->method('provideView')
            ->with($this->equalTo($value), $this->equalTo(array()), $this->equalTo('api'))
            ->will($this->returnValue($view));

        $viewProviders = array($viewProvider1, $viewProvider2);

        $viewTemplateProvider = $this->getMock('Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider');
        $viewTemplateProvider
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($view))
            ->will($this->returnValue(true));
        $viewTemplateProvider
            ->expects($this->once())
            ->method('provideTemplate')
            ->with($this->equalTo($view))
            ->will($this->returnValue('some_template.html.twig'));

        $viewBuilder = new ViewBuilder($viewProviders, array($viewTemplateProvider));
        self::assertEquals($viewWithTemplate, $viewBuilder->buildView($value, array(), 'api'));
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     */
    public function testBuildViewWithNoTemplateViewProviders()
    {
        $value = new Value();
        $view = new View();

        $viewProvider1 = $this->getMock('Netgen\BlockManager\View\Provider\ViewProvider');
        $viewProvider1
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(false));

        $viewProvider2 = $this->getMock('Netgen\BlockManager\View\Provider\ViewProvider');
        $viewProvider2
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(true));
        $viewProvider2
            ->expects($this->once())
            ->method('provideView')
            ->with($this->equalTo($value), $this->equalTo(array()), $this->equalTo('api'))
            ->will($this->returnValue($view));

        $viewProviders = array($viewProvider1, $viewProvider2);

        $viewTemplateProvider = $this->getMock('Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider');
        $viewTemplateProvider
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($view))
            ->will($this->returnValue(false));

        $viewBuilder = new ViewBuilder($viewProviders, array($viewTemplateProvider));
        self::assertEquals($view, $viewBuilder->buildView($value, array(), 'api'));
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @expectedException \InvalidArgumentException
     */
    public function testBuildViewWithNoViewProviders()
    {
        $value = new Value();

        $viewProvider1 = $this->getMock('Netgen\BlockManager\View\Provider\ViewProvider');
        $viewProvider1
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(false));

        $viewProvider2 = $this->getMock('Netgen\BlockManager\View\Provider\ViewProvider');
        $viewProvider2
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(false));

        $viewProviders = array($viewProvider1, $viewProvider2);

        $viewTemplateProvider = $this->getMock('Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider');

        $viewBuilder = new ViewBuilder($viewProviders, array($viewTemplateProvider));
        $viewBuilder->buildView($value, array(), 'api');
    }
}
