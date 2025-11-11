<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View;

use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Provider\ViewProviderInterface;
use Netgen\Layouts\View\TemplateResolverInterface;
use Netgen\Layouts\View\ViewBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[CoversClass(ViewBuilder::class)]
final class ViewBuilderTest extends TestCase
{
    private MockObject&ViewProviderInterface $viewProviderMock;

    private MockObject&TemplateResolverInterface $templateResolverMock;

    private MockObject&EventDispatcherInterface $eventDispatcherMock;

    protected function setUp(): void
    {
        $this->viewProviderMock = $this->createMock(ViewProviderInterface::class);
        $this->templateResolverMock = $this->createMock(TemplateResolverInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
    }

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
                'some_param' => 'some_value',
                'view_context' => $builtView->getContext(),
                'value' => $value,
            ],
            $builtView->getParameters(),
        );
    }

    public function testBuildViewWithNoViewProviders(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('No view providers found for "Netgen\Layouts\Tests\API\Stubs\Value" value.');

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

    public function testBuildViewWithNoViewProvidersThatSupportValue(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('No view providers found for "Netgen\Layouts\Tests\API\Stubs\Value" value.');

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
