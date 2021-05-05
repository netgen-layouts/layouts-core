<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\View;

use Netgen\Layouts\Exception\View\TemplateResolverException;
use PHPUnit\Framework\TestCase;

final class TemplateResolverExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\View\TemplateResolverException::__construct
     */
    public function testConstructor(): void
    {
        $exception = new TemplateResolverException();

        self::assertSame(
            'An error occurred while resolving the view template.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\View\TemplateResolverException::noTemplateMatcher
     */
    public function testNoTemplateMatcher(): void
    {
        $exception = TemplateResolverException::noTemplateMatcher('matcher');

        self::assertSame(
            'No template matcher could be found with identifier "matcher".',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\View\TemplateResolverException::noTemplateMatch
     */
    public function testNoTemplateMatch(): void
    {
        $exception = TemplateResolverException::noTemplateMatch('block_view', 'default');

        self::assertSame(
            'No template match could be found for "block_view" view and context "default".',
            $exception->getMessage(),
        );
    }
}
