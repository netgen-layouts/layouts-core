<?php

namespace Netgen\BlockManager\Tests\Exception\View;

use Netgen\BlockManager\Exception\View\TemplateResolverException;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use PHPUnit\Framework\TestCase;

class TemplateResolverExceptionTest extends TestCase
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
     * @covers \Netgen\BlockManager\Exception\View\TemplateResolverException::invalidTemplateMatcher
     */
    public function testInvalidTemplateMatcher()
    {
        $exception = TemplateResolverException::invalidTemplateMatcher('matcher');

        $this->assertEquals(
            sprintf(
                'Template matcher "matcher" needs to implement "%s" interface.',
                MatcherInterface::class
            ),
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
            sprintf(
                'No template match could be found for "block_view" view and context "default".',
                MatcherInterface::class
            ),
            $exception->getMessage()
        );
    }
}
