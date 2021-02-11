<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Plugin;

use Netgen\Bundle\LayoutsBundle\Templating\Plugin\SimplePlugin;
use PHPUnit\Framework\TestCase;

final class SimplePluginTest extends TestCase
{
    private SimplePlugin $plugin;

    protected function setUp(): void
    {
        $this->plugin = new SimplePlugin('template.html.twig', ['param' => 'value']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Plugin\SimplePlugin::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Plugin\SimplePlugin::getTemplateName
     */
    public function testGetTemplateName(): void
    {
        self::assertSame('template.html.twig', $this->plugin->getTemplateName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Plugin\SimplePlugin::getParameters
     */
    public function testGetParameters(): void
    {
        self::assertSame(['param' => 'value'], $this->plugin->getParameters());
    }
}
