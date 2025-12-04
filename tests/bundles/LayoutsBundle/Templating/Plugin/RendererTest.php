<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Plugin;

use Exception;
use Netgen\Bundle\LayoutsBundle\Templating\Plugin\Renderer;
use Netgen\Bundle\LayoutsBundle\Templating\Plugin\SimplePlugin;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

#[CoversClass(Renderer::class)]
final class RendererTest extends TestCase
{
    private Renderer $renderer;

    protected function setUp(): void
    {
        $twig = new Environment(
            new ArrayLoader(
                [
                    'template1.html.twig' => '{{param}}',
                    'template2.html.twig' => '{{param}}{{param2}}',
                ],
            ),
        );

        $this->renderer = new Renderer(
            $twig,
            [
                'plugin' => [
                    new SimplePlugin('template1.html.twig'),
                    new SimplePlugin('template2.html.twig', ['param2' => 'value2', 'param' => 'value3']),
                ],
            ],
        );
    }

    public function testRenderPlugins(): void
    {
        self::assertSame('valuevalue3value2', $this->renderer->renderPlugins('plugin', ['param' => 'value']));
    }

    public function testRenderPluginsWithUnknownPlugin(): void
    {
        self::assertSame('', $this->renderer->renderPlugins('unknown'));
    }

    public function testRenderPluginsWithException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception message');

        $twigStub = self::createStub(Environment::class);

        $twigStub
            ->method('display')
            ->willThrowException(new Exception('Test exception message'));

        $renderer = new Renderer(
            $twigStub,
            [
                'plugin' => [
                    new SimplePlugin('template.html.twig'),
                ],
            ],
        );

        $renderer->renderPlugins('plugin');
    }
}
