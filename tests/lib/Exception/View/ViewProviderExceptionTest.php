<?php

namespace Netgen\BlockManager\Tests\Exception\View;

use Netgen\BlockManager\Exception\View\ViewProviderException;
use PHPUnit\Framework\TestCase;

class ViewProviderExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\View\ViewProviderException::__construct
     */
    public function testConstructor()
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
    public function testNoViewProvider()
    {
        $exception = ViewProviderException::noViewProvider('some_class');

        $this->assertEquals(
            'No view providers found for "some_class" value object.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\View\ViewProviderException::noParameter
     */
    public function testNoParameter()
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
    public function testInvalidParameter()
    {
        $exception = ViewProviderException::invalidParameter('block', 'param', 'string');

        $this->assertEquals(
            'To build the block view, "param" parameter needs to be of "string" type.',
            $exception->getMessage()
        );
    }
}
