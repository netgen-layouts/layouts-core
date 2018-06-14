<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating;

use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolver;
use PHPUnit\Framework\TestCase;

final class PageLayoutResolverTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolver
     */
    private $resolver;

    public function setUp(): void
    {
        $this->resolver = new PageLayoutResolver('defaultPagelayout');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolver::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayout(): void
    {
        $this->assertEquals('defaultPagelayout', $this->resolver->resolvePageLayout());
    }
}
