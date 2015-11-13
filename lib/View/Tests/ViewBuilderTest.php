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

        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolverInterface');
        $templateResolver
            ->expects($this->once())
            ->method('resolveTemplate')
            ->with($this->equalTo($view))
            ->will($this->returnValue('some_template.html.twig'));

        $viewBuilder = new ViewBuilder(
            $viewProviders,
            array(
                'Netgen\BlockManager\View\Tests\Stubs\View' => $templateResolver,
            )
        );
        self::assertEquals($viewWithTemplate, $viewBuilder->buildView($value, array(), 'api'));
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     */
    public function testBuildViewWithNoTemplateResolvers()
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

        $viewBuilder = new ViewBuilder($viewProviders);
        self::assertEquals($view, $viewBuilder->buildView($value, array(), 'api'));
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     */
    public function testBuildViewWithNoTemplateResolver()
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

        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolverInterface');

        $viewProviders = array($viewProvider1, $viewProvider2);

        $viewBuilder = new ViewBuilder(
            $viewProviders,
            array(
                'Some\Class' => $templateResolver,
            )
        );

        self::assertEquals($view, $viewBuilder->buildView($value, array(), 'api'));
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @expectedException \InvalidArgumentException
     */
    public function testBuildViewThrowsInvalidArgumentExceptionWithNoTemplateResolverInterface()
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

        $templateResolver = $this->getMock('DateTime');

        $viewProviders = array($viewProvider1, $viewProvider2);

        $viewBuilder = new ViewBuilder(
            $viewProviders,
            array(
                'Netgen\BlockManager\View\Tests\Stubs\View' => $templateResolver,
            )
        );

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

        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolverInterface');

        $viewBuilder = new ViewBuilder(
            $viewProviders,
            array(
                'Netgen\BlockManager\View\Tests\Stubs\View' => $templateResolver,
            )
        );

        $viewBuilder->buildView($value, array(), 'api');
    }
}
