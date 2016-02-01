<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Netgen\BlockManager\View\ViewBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Netgen\BlockManager\View\TemplateResolverInterface;

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

        $viewProvider = $this->getMock(ViewProviderInterface::class);
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

        $templateResolver = $this->getMock(TemplateResolverInterface::class);
        $templateResolver
            ->expects($this->once())
            ->method('resolveTemplate')
            ->with($this->equalTo($view))
            ->will($this->returnValue('some_template.html.twig'));

        $eventDispatcherMock = $this->getMock(EventDispatcherInterface::class);
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

        self::assertInstanceOf(View::class, $builtView);
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

        $templateResolver = $this->getMock(TemplateResolverInterface::class);
        $eventDispatcherMock = $this->getMock(EventDispatcherInterface::class);

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

        $viewProvider = $this->getMock(ViewProviderInterface::class);
        $viewProvider
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(false));
        $viewProvider
            ->expects($this->never())
            ->method('provideView');

        $templateResolver = $this->getMock(TemplateResolverInterface::class);
        $eventDispatcherMock = $this->getMock(EventDispatcherInterface::class);

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

        $viewProvider = $this->getMock(DateTime::class);
        $templateResolver = $this->getMock(TemplateResolverInterface::class);
        $eventDispatcherMock = $this->getMock(EventDispatcherInterface::class);

        $viewBuilder = new ViewBuilder(
            array($viewProvider),
            $templateResolver,
            $eventDispatcherMock
        );

        self::assertEquals($view, $viewBuilder->buildView($value, 'context'));
    }
}
