<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Plugin;

use Netgen\Bundle\BlockManagerBundle\Templating\Plugin\SimplePlugin;
use PHPUnit\Framework\TestCase;

final class SimplePluginTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\SimplePlugin
     */
    private $plugin;

    public function setUp(): void
    {
        $this->plugin = new SimplePlugin('template.html.twig', ['param' => 'value']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\SimplePlugin::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\SimplePlugin::getTemplateName
     */
    public function testGetTemplateName(): void
    {
        $this->assertSame('template.html.twig', $this->plugin->getTemplateName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\SimplePlugin::getParameters
     */
    public function testGetParameters(): void
    {
        $this->assertSame(['param' => 'value'], $this->plugin->getParameters());
    }
}
