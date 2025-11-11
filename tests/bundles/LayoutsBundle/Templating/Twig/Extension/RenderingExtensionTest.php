<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension\RenderingExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFunction;

#[CoversClass(RenderingExtension::class)]
final class RenderingExtensionTest extends TestCase
{
    private RenderingExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new RenderingExtension();
    }

    public function testGetFunctions(): void
    {
        self::assertNotEmpty($this->extension->getFunctions());
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }

    public function testGetNodeVisitors(): void
    {
        self::assertNotEmpty($this->extension->getNodeVisitors());
        self::assertContainsOnlyInstancesOf(NodeVisitorInterface::class, $this->extension->getNodeVisitors());
    }

    public function testGetTokenParsers(): void
    {
        self::assertNotEmpty($this->extension->getTokenParsers());
        self::assertContainsOnlyInstancesOf(TokenParserInterface::class, $this->extension->getTokenParsers());
    }
}
