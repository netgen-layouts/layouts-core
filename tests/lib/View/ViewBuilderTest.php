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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[CoversClass(ViewBuilder::class)]
final class ViewBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\Stub&\Netgen\Layouts\View\Provider\ViewProviderInterface<object>
     */
    private Stub&ViewProviderInterface $viewProviderStub;

    private Stub&TemplateResolverInterface $templateResolverStub;

    private Stub&EventDispatcherInterface $eventDispatcherStub;

    protected function setUp(): void
    {
        $this->viewProviderStub = self::createStub(ViewProviderInterface::class);
        $this->templateResolverStub = self::createStub(TemplateResolverInterface::class);
        $this->eventDispatcherStub = self::createStub(EventDispatcherInterface::class);
    }

    public function testBuildView(): void
    {
        $value = new Value();
        $view = new View($value);

        $this->viewProviderStub
            ->method('supports')
            ->with(self::identicalTo($value))
            ->willReturn(true);

        $this->viewProviderStub
            ->method('provideView')
            ->with(self::identicalTo($value))
            ->willReturn($view);

        $this->templateResolverStub
            ->method('resolveTemplate')
            ->with(self::identicalTo($view));

        $viewBuilder = new ViewBuilder(
            $this->templateResolverStub,
            $this->eventDispatcherStub,
            [$this->viewProviderStub],
        );

        $viewParameters = ['some_param' => 'some_value'];
        $builtView = $viewBuilder->buildView($value, 'context', $viewParameters);

        self::assertSame('context', $builtView->context);
        self::assertSame(
            [
                'some_param' => 'some_value',
                'view_context' => $builtView->context,
                'value' => $value,
            ],
            $builtView->parameters,
        );
    }

    public function testBuildViewWithNoViewProviders(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('No view providers found for "Netgen\Layouts\Tests\API\Stubs\Value" value.');

        $value = new Value();

        $viewBuilder = new ViewBuilder(
            $this->templateResolverStub,
            $this->eventDispatcherStub,
            [],
        );

        $viewBuilder->buildView($value);
    }

    public function testBuildViewWithNoViewProvidersThatSupportValue(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('No view providers found for "Netgen\Layouts\Tests\API\Stubs\Value" value.');

        $value = new Value();

        $this->viewProviderStub
            ->method('supports')
            ->with(self::identicalTo($value))
            ->willReturn(false);

        $viewBuilder = new ViewBuilder(
            $this->templateResolverStub,
            $this->eventDispatcherStub,
            [$this->viewProviderStub],
        );

        $viewBuilder->buildView($value);
    }
}
