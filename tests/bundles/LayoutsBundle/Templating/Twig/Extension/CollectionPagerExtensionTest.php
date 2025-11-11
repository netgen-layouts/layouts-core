<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension\CollectionPagerExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

#[CoversClass(CollectionPagerExtension::class)]
final class CollectionPagerExtensionTest extends TestCase
{
    private CollectionPagerExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new CollectionPagerExtension();
    }

    public function testGetFunctions(): void
    {
        self::assertNotEmpty($this->extension->getFunctions());
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }
}
