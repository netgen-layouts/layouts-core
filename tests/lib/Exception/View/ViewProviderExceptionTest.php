<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\View;

use Netgen\Layouts\Exception\View\ViewProviderException;
use PHPUnit\Framework\TestCase;

final class ViewProviderExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\View\ViewProviderException::__construct
     */
    public function testConstructor(): void
    {
        $exception = new ViewProviderException();

        self::assertSame(
            'An error occurred while building the view.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\View\ViewProviderException::noViewProvider
     */
    public function testNoViewProvider(): void
    {
        $exception = ViewProviderException::noViewProvider('some_class');

        self::assertSame(
            'No view providers found for "some_class" value.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\View\ViewProviderException::noParameter
     */
    public function testNoParameter(): void
    {
        $exception = ViewProviderException::noParameter('block', 'param');

        self::assertSame(
            'To build the block view, "param" parameter needs to be provided.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\View\ViewProviderException::invalidParameter
     */
    public function testInvalidParameter(): void
    {
        $exception = ViewProviderException::invalidParameter('block', 'param', 'string');

        self::assertSame(
            'To build the block view, "param" parameter needs to be of "string" type.',
            $exception->getMessage(),
        );
    }
}
