<?php

namespace Netgen\BlockManager\Tests\Exception\View;

use Netgen\BlockManager\Exception\View\ViewException;
use PHPUnit\Framework\TestCase;

final class ViewExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\View\ViewException::parameterNotFound
     */
    public function testParameterNotFound()
    {
        $exception = ViewException::parameterNotFound('param', 'view');

        $this->assertEquals(
            'Parameter with "param" name was not found in "view" view.',
            $exception->getMessage()
        );
    }
}
