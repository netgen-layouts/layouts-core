<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating;

use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolver;
use PHPUnit\Framework\TestCase;

class PageLayoutResolverTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolver
     */
    protected $resolver;

    public function setUp()
    {
        $this->resolver = new PageLayoutResolver('defaultPagelayout');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolver::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayout()
    {
        $this->assertEquals('defaultPagelayout', $this->resolver->resolvePageLayout());
    }
}
