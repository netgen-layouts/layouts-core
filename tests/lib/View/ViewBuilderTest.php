<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Provider\ViewProviderInterface;
use Netgen\Layouts\View\TemplateResolverInterface;
use Netgen\Layouts\View\ViewBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Kernel;

use function array_reverse;
use function sprintf;

final class ViewBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\View\Provider\ViewProviderInterface
     */
    private MockObject $viewProviderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\View\TemplateResolverInterface
     */
    private MockObject $templateResolverMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private MockObject $eventDispatcherMock;

    protected function setUp(): void
    {
        $this->viewProviderMock = $this->createMock(ViewProviderInterface::class);
        $this->templateResolverMock = $this->createMock(TemplateResolverInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @covers \Netgen\Layouts\View\ViewBuilder::__construct
     * @covers \Netgen\Layouts\View\ViewBuilder::buildView
     * @covers \Netgen\Layouts\View\ViewBuilder::getViewProvider
     */
    public function testBuildView(): void
    {
        $value = new Value();
        $view = new View($value);

        $this->viewProviderMock
            ->expects(self::once())
            ->method('supports')
            ->with(self::identicalTo($value))
            ->willReturn(true);

        $this->viewProviderMock
            ->expects(self::once())
            ->method('provideView')
            ->with(self::identicalTo($value))
            ->willReturn($view);

        $this->templateResolverMock
            ->expects(self::once())
            ->method('resolveTemplate')
            ->with(self::identicalTo($view));

        $args = [
            self::isInstanceOf(CollectViewParametersEvent::class),
            self::identicalTo(LayoutsEvents::BUILD_VIEW),
        ];

        if (Kernel::VERSION_ID < 40300) {
            $args = array_reverse($args);
        }

        $consecutiveArgs = [$args];

        $args = [
            self::isInstanceOf(CollectViewParametersEvent::class),
            self::identicalTo(sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'stub')),
        ];

        if (Kernel::VERSION_ID < 40300) {
            $args = array_reverse($args);
        }

        $consecutiveArgs[] = $args;

        $this->eventDispatcherMock
            ->method('dispatch')
            ->withConsecutive(...$consecutiveArgs);

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            [$this->viewProviderMock],
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
            $builtView->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\View\ViewBuilder::buildView
     * @covers \Netgen\Layouts\View\ViewBuilder::getViewProvider
     */
    public function testBuildViewWithNoViewProviders(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('No view providers found for "Netgen\\Layouts\\Tests\\API\\Stubs\\Value" value.');

        $value = new Value();

        $this->templateResolverMock
            ->expects(self::never())
            ->method('resolveTemplate');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            [],
        );

        $viewBuilder->buildView($value);
    }

    /**
     * @covers \Netgen\Layouts\View\ViewBuilder::buildView
     * @covers \Netgen\Layouts\View\ViewBuilder::getViewProvider
     */
    public function testBuildViewWithNoViewProvidersThatSupportValue(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('No view providers found for "Netgen\\Layouts\\Tests\\API\\Stubs\\Value" value.');

        $value = new Value();

        $this->viewProviderMock
            ->expects(self::once())
            ->method('supports')
            ->with(self::identicalTo($value))
            ->willReturn(false);

        $this->viewProviderMock
            ->expects(self::never())
            ->method('provideView');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            [$this->viewProviderMock],
        );

        $viewBuilder->buildView($value);
    }
}
