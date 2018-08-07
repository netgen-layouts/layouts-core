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
        $view = new View($value);

        $this->viewProviderMock
            ->expects(self::once())
            ->method('supports')
            ->with(self::identicalTo($value))
            ->will(self::returnValue(true));

        $this->viewProviderMock
            ->expects(self::once())
            ->method('provideView')
            ->with(self::identicalTo($value))
            ->will(self::returnValue($view));

        $this->templateResolverMock
            ->expects(self::once())
            ->method('resolveTemplate')
            ->with(self::identicalTo($view));

        $this->eventDispatcherMock
            ->expects(self::at(0))
            ->method('dispatch')
            ->with(
                self::identicalTo(BlockManagerEvents::BUILD_VIEW),
                self::isInstanceOf(CollectViewParametersEvent::class)
            );

        $this->eventDispatcherMock
            ->expects(self::at(1))
            ->method('dispatch')
            ->with(
                self::identicalTo(sprintf('%s.%s', BlockManagerEvents::BUILD_VIEW, 'stub')),
                self::isInstanceOf(CollectViewParametersEvent::class)
            );

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            [$this->viewProviderMock]
        );

        $viewParameters = ['some_param' => 'some_value'];
        $builtView = $viewBuilder->buildView($value, 'context', $viewParameters);

        self::assertSame('context', $builtView->getContext());
        self::assertSame(
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
            ->expects(self::never())
            ->method('resolveTemplate');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            []
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
            ->expects(self::once())
            ->method('supports')
            ->with(self::identicalTo($value))
            ->will(self::returnValue(false));

        $this->viewProviderMock
            ->expects(self::never())
            ->method('provideView');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            [$this->viewProviderMock]
        );

        $viewBuilder->buildView($value);
    }
}
