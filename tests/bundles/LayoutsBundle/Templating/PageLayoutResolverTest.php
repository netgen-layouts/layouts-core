<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating;

use Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolver;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PageLayoutResolver::class)]
final class PageLayoutResolverTest extends TestCase
{
    private PageLayoutResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new PageLayoutResolver('defaultPagelayout');
    }

    public function testResolvePageLayout(): void
    {
        self::assertSame('defaultPagelayout', $this->resolver->resolvePageLayout());
    }

    public function testResolvePageLayoutThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Base page layout not specified. To render the page with Netgen Layouts, specify the base page layout with "netgen_layouts.pagelayout" semantic config.');

        $resolver = new PageLayoutResolver('');
        $resolver->resolvePageLayout();
    }
}
