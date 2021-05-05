<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\View;

use Netgen\Layouts\Exception\View\ViewException;
use PHPUnit\Framework\TestCase;

final class ViewExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\View\ViewException::parameterNotFound
     */
    public function testParameterNotFound(): void
    {
        $exception = ViewException::parameterNotFound('param', 'view');

        self::assertSame(
            'Parameter with "param" name was not found in "view" view.',
            $exception->getMessage(),
        );
    }
}
