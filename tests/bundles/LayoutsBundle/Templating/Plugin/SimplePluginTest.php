<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Plugin;

use Netgen\Bundle\LayoutsBundle\Templating\Plugin\SimplePlugin;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SimplePlugin::class)]
final class SimplePluginTest extends TestCase
{
    private SimplePlugin $plugin;

    protected function setUp(): void
    {
        $this->plugin = new SimplePlugin('template.html.twig', ['param' => 'value']);
    }

    public function testGetTemplateName(): void
    {
        self::assertSame('template.html.twig', $this->plugin->templateName);
    }

    public function testGetParameters(): void
    {
        self::assertSame(['param' => 'value'], $this->plugin->parameters);
    }
}
