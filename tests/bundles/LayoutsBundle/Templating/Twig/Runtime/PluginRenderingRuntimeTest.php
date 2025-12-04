<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime;

use Exception;
use Netgen\Bundle\LayoutsBundle\Templating\Plugin\RendererInterface;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\PluginRenderingRuntime;
use Netgen\Layouts\Tests\Stubs\ErrorHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(PluginRenderingRuntime::class)]
final class PluginRenderingRuntimeTest extends TestCase
{
    private Stub&RendererInterface $pluginRendererStub;

    private ErrorHandler $errorHandler;

    private PluginRenderingRuntime $runtime;

    protected function setUp(): void
    {
        $this->pluginRendererStub = self::createStub(RendererInterface::class);
        $this->errorHandler = new ErrorHandler();

        $this->runtime = new PluginRenderingRuntime(
            $this->pluginRendererStub,
            $this->errorHandler,
        );
    }

    public function testRenderPlugins(): void
    {
        $this->pluginRendererStub
            ->method('renderPlugins')
            ->with(
                self::identicalTo('plugin_name'),
                self::identicalTo(['param' => 'value']),
            )
            ->willReturn('rendered plugin');

        self::assertSame(
            'rendered plugin',
            $this->runtime->renderPlugins(
                ['param' => 'value'],
                'plugin_name',
            ),
        );
    }

    public function testRenderPluginsThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);

        $this->pluginRendererStub
            ->method('renderPlugins')
            ->with(
                self::identicalTo('plugin_name'),
                self::identicalTo(['param' => 'value']),
            )
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderPlugins(
            ['param' => 'value'],
            'plugin_name',
        );
    }

    public function testRenderPluginsReturnsEmptyStringOnException(): void
    {
        $this->pluginRendererStub
            ->method('renderPlugins')
            ->with(
                self::identicalTo('plugin_name'),
                self::identicalTo(['param' => 'value']),
            )
            ->willThrowException(new Exception('Test exception text'));

        self::assertSame('', $this->runtime->renderPlugins(['param' => 'value'], 'plugin_name'));
    }
}
