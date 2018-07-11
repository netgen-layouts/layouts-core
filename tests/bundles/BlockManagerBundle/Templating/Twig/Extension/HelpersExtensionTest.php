<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\HelpersExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

final class HelpersExtensionTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\HelpersExtension
     */
    private $extension;

    public function setUp(): void
    {
        $this->extension = new HelpersExtension();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\HelpersExtension::getFilters
     */
    public function testGetFilters(): void
    {
        $this->assertNotEmpty($this->extension->getFilters());

        foreach ($this->extension->getFilters() as $filter) {
            $this->assertInstanceOf(TwigFilter::class, $filter);
        }
    }
}
