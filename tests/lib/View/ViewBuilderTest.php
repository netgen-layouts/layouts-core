<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Netgen\BlockManager\View\TemplateResolverInterface;
use Netgen\BlockManager\View\ViewBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ViewBuilderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $viewProviderMock;

    /**
     * @var \Netgen\BlockManager\View\TemplateResolverInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $templateResolverMock;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $eventDispatcherMock;

    public function setUp()
    {
        $this->viewProviderMock = $this->createMock(ViewProviderInterface::class);
        $this->templateResolverMock = $this->createMock(TemplateResolverInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @expectedException \Netgen\BlockManager\Exception\InvalidInterfaceException
     * @expectedExceptionMessage View provider "stdClass" needs to implement "Netgen\BlockManager\View\Provider\ViewProviderInterface" interface.
     */
    public function testConstructorThrowsInvalidInterfaceExceptionWithNoViewProviderInterface()
    {
        new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            [new stdClass()]
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @covers \Netgen\BlockManager\View\ViewBuilder::getViewProvider
     */
    public function testBuildView()
    {
        $value = new Value();
        $view = new View(['value' => $value]);

        $this->viewProviderMock
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(true));

        $this->viewProviderMock
            ->expects($this->once())
            ->method('provideView')
            ->with($this->equalTo($value))
            ->will($this->returnValue($view));

        $this->templateResolverMock
            ->expects($this->once())
            ->method('resolveTemplate')
            ->with($this->equalTo($view));

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            [$this->viewProviderMock]
        );

        $viewParameters = ['some_param' => 'some_value'];
        $builtView = $viewBuilder->buildView($value, 'context', $viewParameters);

        $this->assertInstanceOf(View::class, $builtView);
        $this->assertEquals('context', $builtView->getContext());
        $this->assertEquals(
            [
                'value' => new Value(),
                'view_context' => $builtView->getContext(),
            ] + $viewParameters,
            $builtView->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @covers \Netgen\BlockManager\View\ViewBuilder::getViewProvider
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage No view providers found for "Netgen\BlockManager\Tests\Core\Stubs\Value" value.
     */
    public function testBuildViewWithNoViewProviders()
    {
        $value = new Value();

        $this->templateResolverMock
            ->expects($this->never())
            ->method('resolveTemplate');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock
        );

        $viewBuilder->buildView($value);
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @covers \Netgen\BlockManager\View\ViewBuilder::getViewProvider
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage No view providers found for "Netgen\BlockManager\Tests\Core\Stubs\Value" value.
     */
    public function testBuildViewWithNoViewProvidersThatSupportValue()
    {
        $value = new Value();

        $this->viewProviderMock
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(false));

        $this->viewProviderMock
            ->expects($this->never())
            ->method('provideView');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            [$this->viewProviderMock]
        );

        $viewBuilder->buildView($value);
    }
}
