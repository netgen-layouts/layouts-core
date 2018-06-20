<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Netgen\BlockManager\View\TemplateResolverInterface;
use Netgen\BlockManager\View\ViewBuilder;
use PHPUnit\Framework\TestCase;
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

    public function setUp(): void
    {
        $this->viewProviderMock = $this->createMock(ViewProviderInterface::class);
        $this->templateResolverMock = $this->createMock(TemplateResolverInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @covers \Netgen\BlockManager\View\ViewBuilder::getViewProvider
     */
    public function testBuildView(): void
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
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->equalTo(BlockManagerEvents::BUILD_VIEW),
                $this->isInstanceOf(CollectViewParametersEvent::class)
            );

        $this->eventDispatcherMock
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->equalTo(sprintf('%s.%s', BlockManagerEvents::BUILD_VIEW, 'stub')),
                $this->isInstanceOf(CollectViewParametersEvent::class)
            );

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            [$this->viewProviderMock]
        );

        $viewParameters = ['some_param' => 'some_value'];
        $builtView = $viewBuilder->buildView($value, 'context', $viewParameters);

        $this->assertInstanceOf(View::class, $builtView);
        $this->assertSame('context', $builtView->getContext());
        $this->assertSame(
            [
                'value' => $value,
                'some_param' => 'some_value',
                'view_context' => $builtView->getContext(),
            ],
            $builtView->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @covers \Netgen\BlockManager\View\ViewBuilder::getViewProvider
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage No view providers found for "Netgen\BlockManager\Tests\Core\Stubs\Value" value.
     */
    public function testBuildViewWithNoViewProviders(): void
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
    public function testBuildViewWithNoViewProvidersThatSupportValue(): void
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
