<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension\PluginRenderingExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

final class PluginRenderingExtensionTest extends TestCase
{
    private PluginRenderingExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new PluginRenderingExtension();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension\PluginRenderingExtension::getFunctions
     */
    public function testGetFunctions(): void
    {
        self::assertNotEmpty($this->extension->getFunctions());
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }
}
