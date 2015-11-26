<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\ViewBuilder;

class ViewBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     */
    public function testBuildView()
    {
        $value = new Value();
        $view = new View();

        $viewProvider = $this->getMock('Netgen\BlockManager\View\Provider\ViewProviderInterface');
        $viewProvider
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(true));
        $viewProvider
            ->expects($this->once())
            ->method('provideView')
            ->with($this->equalTo($value))
            ->will($this->returnValue($view));

        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolver\TemplateResolverInterface');
        $templateResolver
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($view))
            ->will($this->returnValue(true));
        $templateResolver
            ->expects($this->once())
            ->method('resolveTemplate')
            ->with($this->equalTo($view))
            ->will($this->returnValue('some_template.html.twig'));

        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch');

        $viewBuilder = new ViewBuilder(
            array($viewProvider),
            array($templateResolver),
            $eventDispatcherMock
        );

        $viewParameters = array('some_param' => 'some_value');
        $builtView = $viewBuilder->buildView($value, $viewParameters, 'api');

        self::assertInstanceOf('Netgen\BlockManager\Tests\View\Stubs\View', $builtView);
        self::assertEquals('some_template.html.twig', $builtView->getTemplate());
        self::assertEquals('api', $builtView->getContext());
        self::assertEquals($viewParameters, $builtView->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     */
    public function testBuildViewWithNoTemplateResolvers()
    {
        $value = new Value();
        $view = new View();

        $viewProvider = $this->getMock('Netgen\BlockManager\View\Provider\ViewProviderInterface');
        $viewProvider
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(true));
        $viewProvider
            ->expects($this->once())
            ->method('provideView')
            ->with($this->equalTo($value))
            ->will($this->returnValue($view));

        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch');

        $viewBuilder = new ViewBuilder(array($viewProvider), array(), $eventDispatcherMock);

        $viewParameters = array('some_param' => 'some_value');
        $builtView = $viewBuilder->buildView($value, $viewParameters, 'api');

        self::assertInstanceOf('Netgen\BlockManager\Tests\View\Stubs\View', $builtView);
        self::assertNull($builtView->getTemplate());
        self::assertEquals('api', $builtView->getContext());
        self::assertEquals($viewParameters, $builtView->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     */
    public function testBuildViewWithNoTemplateResolverThatSupportsView()
    {
        $value = new Value();
        $view = new View();

        $viewProvider = $this->getMock('Netgen\BlockManager\View\Provider\ViewProviderInterface');
        $viewProvider
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(true));
        $viewProvider
            ->expects($this->once())
            ->method('provideView')
            ->with($this->equalTo($value))
            ->will($this->returnValue($view));

        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolver\TemplateResolverInterface');
        $templateResolver
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($view))
            ->will($this->returnValue(false));
        $templateResolver
            ->expects($this->never())
            ->method('resolveTemplate');

        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch');

        $viewBuilder = new ViewBuilder(
            array($viewProvider),
            array($templateResolver),
            $eventDispatcherMock
        );

        $viewParameters = array('some_param' => 'some_value');
        $builtView = $viewBuilder->buildView($value, $viewParameters, 'api');

        self::assertInstanceOf('Netgen\BlockManager\Tests\View\Stubs\View', $builtView);
        self::assertNull($builtView->getTemplate());
        self::assertEquals('api', $builtView->getContext());
        self::assertEquals($viewParameters, $builtView->getParameters());
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

        $viewProvider = $this->getMock('Netgen\BlockManager\View\Provider\ViewProviderInterface');
        $viewProvider
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(true));
        $viewProvider
            ->expects($this->once())
            ->method('provideView')
            ->with($this->equalTo($value))
            ->will($this->returnValue($view));

        $templateResolver = $this->getMock('DateTime');

        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch');

        $viewBuilder = new ViewBuilder(
            array($viewProvider),
            array($templateResolver),
            $eventDispatcherMock
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

        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolver\TemplateResolverInterface');
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $viewBuilder = new ViewBuilder(
            array(),
            array($templateResolver),
            $eventDispatcherMock
        );

        $viewBuilder->buildView($value, array(), 'api');
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @expectedException \InvalidArgumentException
     */
    public function testBuildViewWithNoViewProvidersThatSupportValue()
    {
        $value = new Value();

        $viewProvider = $this->getMock('Netgen\BlockManager\View\Provider\ViewProviderInterface');
        $viewProvider
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(false));
        $viewProvider
            ->expects($this->never())
            ->method('provideView');

        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolver\TemplateResolverInterface');
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $viewBuilder = new ViewBuilder(
            array($viewProvider),
            array($templateResolver),
            $eventDispatcherMock
        );

        $viewBuilder->buildView($value, array(), 'api');
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @expectedException \InvalidArgumentException
     */
    public function testBuildViewThrowsInvalidArgumentExceptionWithNoViewProviderInterface()
    {
        $value = new Value();
        $view = new View();

        $viewProvider = $this->getMock('DateTime');
        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolver\TemplateResolverInterface');
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $viewBuilder = new ViewBuilder(
            array($viewProvider),
            array($templateResolver),
            $eventDispatcherMock
        );

        self::assertEquals($view, $viewBuilder->buildView($value, array(), 'api'));
    }
}
