<?php

namespace Netgen\BlockManager\Tests\Exception\View;

use Netgen\BlockManager\Exception\View\TemplateResolverException;
use PHPUnit\Framework\TestCase;

final class TemplateResolverExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\View\TemplateResolverException::__construct
     */
    public function testConstructor()
    {
        $exception = new TemplateResolverException();

        $this->assertEquals(
            'An error occurred while resolving the view template.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\View\TemplateResolverException::noTemplateMatcher
     */
    public function testNoTemplateMatcher()
    {
        $exception = TemplateResolverException::noTemplateMatcher('matcher');

        $this->assertEquals(
            'No template matcher could be found with identifier "matcher".',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\View\TemplateResolverException::noTemplateMatch
     */
    public function testNoTemplateMatch()
    {
        $exception = TemplateResolverException::noTemplateMatch('block_view', 'default');

        $this->assertEquals(
            'No template match could be found for "block_view" view and context "default".',
            $exception->getMessage()
        );
    }
}
