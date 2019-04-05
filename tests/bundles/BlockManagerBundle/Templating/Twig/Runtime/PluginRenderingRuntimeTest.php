<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Exception;
use Netgen\BlockManager\Tests\Stubs\ErrorHandler;
use Netgen\Bundle\BlockManagerBundle\Templating\Plugin\RendererInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\PluginRenderingRuntime;
use PHPUnit\Framework\TestCase;

final class PluginRenderingRuntimeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $pluginRendererMock;

    /**
     * @var \Netgen\BlockManager\Tests\Stubs\ErrorHandler
     */
    private $errorHandler;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\PluginRenderingRuntime
     */
    private $runtime;

    public function setUp(): void
    {
        $this->pluginRendererMock = $this->createMock(RendererInterface::class);
        $this->errorHandler = new ErrorHandler();

        $this->runtime = new PluginRenderingRuntime(
            $this->pluginRendererMock,
            $this->errorHandler
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\PluginRenderingRuntime::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\PluginRenderingRuntime::renderPlugins
     */
    public function testRenderPlugins(): void
    {
        $this->pluginRendererMock
            ->expects(self::once())
            ->method('renderPlugins')
            ->with(
                self::identicalTo('plugin_name'),
                self::identicalTo(['param' => 'value'])
            )
            ->willReturn('rendered plugin');

        self::assertSame(
            'rendered plugin',
            $this->runtime->renderPlugins(
                ['param' => 'value'],
                'plugin_name'
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\PluginRenderingRuntime::renderPlugins
     */
    public function testRenderPluginsThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);

        $this->pluginRendererMock
            ->expects(self::once())
            ->method('renderPlugins')
            ->with(
                self::identicalTo('plugin_name'),
                self::identicalTo(['param' => 'value'])
            )
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderPlugins(
            ['param' => 'value'],
            'plugin_name'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\PluginRenderingRuntime::renderPlugins
     */
    public function testRenderPluginsReturnsEmptyStringOnException(): void
    {
        $this->pluginRendererMock
            ->expects(self::once())
            ->method('renderPlugins')
            ->with(
                self::identicalTo('plugin_name'),
                self::identicalTo(['param' => 'value'])
            )
            ->willThrowException(new Exception('Test exception text'));

        self::assertSame('', $this->runtime->renderPlugins(['param' => 'value'], 'plugin_name'));
    }
}
