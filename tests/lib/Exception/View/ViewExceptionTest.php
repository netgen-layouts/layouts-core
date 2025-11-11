<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\View;

use Netgen\Layouts\Exception\View\ViewException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ViewException::class)]
final class ViewExceptionTest extends TestCase
{
    public function testParameterNotFound(): void
    {
        $exception = ViewException::parameterNotFound('param', 'view');

        self::assertSame(
            'Parameter with "param" name was not found in "view" view.',
            $exception->getMessage(),
        );
    }
}
