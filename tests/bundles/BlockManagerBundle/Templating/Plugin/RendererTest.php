<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Plugin;

use Exception;
use Netgen\Bundle\BlockManagerBundle\Templating\Plugin\Renderer;
use Netgen\Bundle\BlockManagerBundle\Templating\Plugin\SimplePlugin;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class RendererTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $twigMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\RendererInterface
     */
    private $renderer;

    public function setUp(): void
    {
        $this->twigMock = $this->createMock(Environment::class);

        $this->renderer = new Renderer(
            $this->twigMock,
            [
                'plugin' => [
                    new SimplePlugin('template1.html.twig'),
                    new SimplePlugin('template2.html.twig', ['param2' => 'value2', 'param' => 'value3']),
                ],
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\Renderer::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\Renderer::renderPlugins
     */
    public function testRenderPlugins(): void
    {
        $this->twigMock
            ->expects($this->at(0))
            ->method('display')
            ->with(
                $this->identicalTo('template1.html.twig'),
                $this->identicalTo(['param' => 'value'])
            )
            ->will(
                $this->returnCallback(
                    function (): void {
                        echo 'rendered1';
                    }
                )
            );

        $this->twigMock
            ->expects($this->at(1))
            ->method('display')
            ->with(
                $this->identicalTo('template2.html.twig'),
                $this->identicalTo(['param2' => 'value2', 'param' => 'value3'])
            )
            ->will(
                $this->returnCallback(
                    function (): void {
                        echo 'rendered2';
                    }
                )
            );

        $this->assertSame('rendered1rendered2', $this->renderer->renderPlugins('plugin', ['param' => 'value']));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\Renderer::renderPlugins
     */
    public function testRenderPluginsWithUnknownPlugin(): void
    {
        $this->twigMock
            ->expects($this->never())
            ->method('display');

        $this->assertSame('', $this->renderer->renderPlugins('unknown'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\Renderer::renderPlugins
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception message
     */
    public function testRenderPluginsWithException(): void
    {
        $this->twigMock
            ->expects($this->at(0))
            ->method('display')
            ->will($this->throwException(new Exception('Test exception message')));

        $this->renderer->renderPlugins('plugin');
    }
}
