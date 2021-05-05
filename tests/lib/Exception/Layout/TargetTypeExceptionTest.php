<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Layout;

use Netgen\Layouts\Exception\Layout\TargetTypeException;
use PHPUnit\Framework\TestCase;

final class TargetTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Layout\TargetTypeException::noTargetType
     */
    public function testNoTargetType(): void
    {
        $exception = TargetTypeException::noTargetType('type');

        self::assertSame(
            'Target type "type" does not exist.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\Layout\TargetTypeException::noFormMapper
     */
    public function testNoFormMapper(): void
    {
        $exception = TargetTypeException::noFormMapper('type');

        self::assertSame(
            'Form mapper for "type" target type does not exist.',
            $exception->getMessage(),
        );
    }
}
