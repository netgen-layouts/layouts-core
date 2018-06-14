<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use PHPUnit\Framework\TestCase;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFunction;

final class RenderingExtensionTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension
     */
    private $extension;

    public function setUp(): void
    {
        $this->extension = new RenderingExtension();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getFunctions
     */
    public function testGetFunctions(): void
    {
        $this->assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getTokenParsers
     */
    public function testGetTokenParsers(): void
    {
        $this->assertNotEmpty($this->extension->getTokenParsers());

        foreach ($this->extension->getTokenParsers() as $tokenParser) {
            $this->assertInstanceOf(TokenParserInterface::class, $tokenParser);
        }
    }
}
