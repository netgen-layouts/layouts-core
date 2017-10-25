<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\CollectionPagerExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class CollectionPagerExtensionTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\CollectionPagerExtension
     */
    private $extension;

    public function setUp()
    {
        $this->extension = new CollectionPagerExtension();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\CollectionPagerExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $this->assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }
}
