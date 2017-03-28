<?php

namespace Netgen\BlockManager\Tests\Exception\View;

use Netgen\BlockManager\Exception\View\ViewProviderException;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
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
     * @covers \Netgen\BlockManager\Exception\View\ViewProviderException::invalidViewProvider
     */
    public function testInvalidViewProvider()
    {
        $exception = ViewProviderException::invalidViewProvider('some_class');

        $this->assertEquals(
            sprintf(
                'View provider "some_class" needs to implement "%s" interface.',
                ViewProviderInterface::class
            ),
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
            sprintf(
                'To build the block view, "param" parameter needs to be provided.',
                MatcherInterface::class
            ),
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
            sprintf(
                'To build the block view, "param" parameter needs to be of "string" type.',
                MatcherInterface::class
            ),
            $exception->getMessage()
        );
    }
}
