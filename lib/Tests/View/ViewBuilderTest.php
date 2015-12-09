<?php

namespace Netgen\BlockManager\Tests\View;

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

        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolverInterface');
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
            $templateResolver,
            $eventDispatcherMock
        );

        $viewParameters = array('some_param' => 'some_value');
        $builtView = $viewBuilder->buildView($value, 'context', $viewParameters);

        self::assertInstanceOf('Netgen\BlockManager\Tests\View\Stubs\View', $builtView);
        self::assertEquals('some_template.html.twig', $builtView->getTemplate());
        self::assertEquals('context', $builtView->getContext());
        self::assertEquals($viewParameters, $builtView->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @expectedException \RuntimeException
     */
    public function testBuildViewWithNoViewProviders()
    {
        $value = new Value();

        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolverInterface');
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $viewBuilder = new ViewBuilder(
            array(),
            $templateResolver,
            $eventDispatcherMock
        );

        $viewBuilder->buildView($value, 'context');
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @expectedException \RuntimeException
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

        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolverInterface');
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $viewBuilder = new ViewBuilder(
            array($viewProvider),
            $templateResolver,
            $eventDispatcherMock
        );

        $viewBuilder->buildView($value, 'context');
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @expectedException \RuntimeException
     */
    public function testBuildViewThrowsRuntimeExceptionWithNoViewProviderInterface()
    {
        $value = new Value();
        $view = new View();

        $viewProvider = $this->getMock('DateTime');
        $templateResolver = $this->getMock('Netgen\BlockManager\View\TemplateResolverInterface');
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $viewBuilder = new ViewBuilder(
            array($viewProvider),
            $templateResolver,
            $eventDispatcherMock
        );

        self::assertEquals($view, $viewBuilder->buildView($value, 'context'));
    }
}
