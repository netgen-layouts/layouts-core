<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Plugin;

use Exception;
use Netgen\Bundle\LayoutsBundle\Templating\Plugin\Renderer;
use Netgen\Bundle\LayoutsBundle\Templating\Plugin\SimplePlugin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class RendererTest extends TestCase
{
    private MockObject $twigMock;

    private Renderer $renderer;

    protected function setUp(): void
    {
        $this->twigMock = $this->createMock(Environment::class);

        $this->renderer = new Renderer(
            $this->twigMock,
            [
                'plugin' => [
                    new SimplePlugin('template1.html.twig'),
                    new SimplePlugin('template2.html.twig', ['param2' => 'value2', 'param' => 'value3']),
                ],
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Plugin\Renderer::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Plugin\Renderer::renderPlugins
     */
    public function testRenderPlugins(): void
    {
        $mock = $this->twigMock
            ->method('display');

        $mock
            ->withConsecutive(
                [
                    self::identicalTo('template1.html.twig'),
                    self::identicalTo(['param' => 'value']),
                ],
                [
                    self::identicalTo('template2.html.twig'),
                    self::identicalTo(['param2' => 'value2', 'param' => 'value3']),
                ],
            )
            ->willReturnOnConsecutiveCalls(
                self::returnCallback(
                    static function (): void {
                        echo 'rendered1';
                    },
                ),
                self::returnCallback(
                    static function (): void {
                        echo 'rendered2';
                    },
                ),
            );

        self::assertSame('rendered1rendered2', $this->renderer->renderPlugins('plugin', ['param' => 'value']));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Plugin\Renderer::renderPlugins
     */
    public function testRenderPluginsWithUnknownPlugin(): void
    {
        $this->twigMock
            ->expects(self::never())
            ->method('display');

        self::assertSame('', $this->renderer->renderPlugins('unknown'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Plugin\Renderer::renderPlugins
     */
    public function testRenderPluginsWithException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception message');

        $this->twigMock
            ->method('display')
            ->willThrowException(new Exception('Test exception message'));

        $this->renderer->renderPlugins('plugin');
    }
}
