<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension\ItemExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

#[CoversClass(ItemExtension::class)]
final class ItemExtensionTest extends TestCase
{
    private ItemExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new ItemExtension();
    }

    public function testGetFunctions(): void
    {
        self::assertNotEmpty($this->extension->getFunctions());
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }
}
