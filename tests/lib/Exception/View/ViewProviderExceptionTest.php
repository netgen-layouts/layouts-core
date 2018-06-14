<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\View;

use Netgen\BlockManager\Exception\View\ViewProviderException;
use PHPUnit\Framework\TestCase;

final class ViewProviderExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\View\ViewProviderException::__construct
     */
    public function testConstructor(): void
    {
        $exception = new ViewProviderException();

        $this->assertEquals(
            'An error occurred while building the view.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\View\ViewProviderException::noViewProvider
     */
    public function testNoViewProvider(): void
    {
        $exception = ViewProviderException::noViewProvider('some_class');

        $this->assertEquals(
            'No view providers found for "some_class" value.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\View\ViewProviderException::noParameter
     */
    public function testNoParameter(): void
    {
        $exception = ViewProviderException::noParameter('block', 'param');

        $this->assertEquals(
            'To build the block view, "param" parameter needs to be provided.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\View\ViewProviderException::invalidParameter
     */
    public function testInvalidParameter(): void
    {
        $exception = ViewProviderException::invalidParameter('block', 'param', 'string');

        $this->assertEquals(
            'To build the block view, "param" parameter needs to be of "string" type.',
            $exception->getMessage()
        );
    }
}
