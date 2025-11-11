<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\View;

use Netgen\Layouts\Exception\View\ViewProviderException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ViewProviderException::class)]
final class ViewProviderExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $exception = new ViewProviderException();

        self::assertSame(
            'An error occurred while building the view.',
            $exception->getMessage(),
        );
    }

    public function testNoViewProvider(): void
    {
        $exception = ViewProviderException::noViewProvider('some_class');

        self::assertSame(
            'No view providers found for "some_class" value.',
            $exception->getMessage(),
        );
    }

    public function testNoParameter(): void
    {
        $exception = ViewProviderException::noParameter('block', 'param');

        self::assertSame(
            'To build the block view, "param" parameter needs to be provided.',
            $exception->getMessage(),
        );
    }

    public function testInvalidParameter(): void
    {
        $exception = ViewProviderException::invalidParameter('block', 'param', 'string');

        self::assertSame(
            'To build the block view, "param" parameter needs to be of "string" type.',
            $exception->getMessage(),
        );
    }
}
