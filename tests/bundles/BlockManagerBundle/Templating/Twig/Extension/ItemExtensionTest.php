<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

final class ItemExtensionTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension
     */
    private $extension;

    public function setUp()
    {
        $this->extension = new ItemExtension();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\ItemExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $this->assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }
}
