<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension\HelpersExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class HelpersExtensionTest extends TestCase
{
    private HelpersExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new HelpersExtension();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension\HelpersExtension::getFilters
     */
    public function testGetFilters(): void
    {
        self::assertNotEmpty($this->extension->getFilters());
        self::assertContainsOnlyInstancesOf(TwigFilter::class, $this->extension->getFilters());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension\HelpersExtension::getFunctions
     */
    public function testGetFunctions(): void
    {
        self::assertNotEmpty($this->extension->getFunctions());
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }
}
