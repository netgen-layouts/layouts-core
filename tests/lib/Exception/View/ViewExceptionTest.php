<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\View;

use Netgen\BlockManager\Exception\View\ViewException;
use PHPUnit\Framework\TestCase;

final class ViewExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\View\ViewException::parameterNotFound
     */
    public function testParameterNotFound(): void
    {
        $exception = ViewException::parameterNotFound('param', 'view');

        self::assertSame(
            'Parameter with "param" name was not found in "view" view.',
            $exception->getMessage()
        );
    }
}
