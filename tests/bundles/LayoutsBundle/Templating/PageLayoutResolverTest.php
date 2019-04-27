<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating;

use Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolver;
use PHPUnit\Framework\TestCase;

final class PageLayoutResolverTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolver
     */
    private $resolver;

    protected function setUp(): void
    {
        $this->resolver = new PageLayoutResolver('defaultPagelayout');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolver::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayout(): void
    {
        self::assertSame('defaultPagelayout', $this->resolver->resolvePageLayout());
    }
}
