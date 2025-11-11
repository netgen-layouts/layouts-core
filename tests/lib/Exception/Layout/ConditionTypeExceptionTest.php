<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Layout;

use Netgen\Layouts\Exception\Layout\ConditionTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConditionTypeException::class)]
final class ConditionTypeExceptionTest extends TestCase
{
    public function testNoConditionType(): void
    {
        $exception = ConditionTypeException::noConditionType('type');

        self::assertSame(
            'Condition type "type" does not exist.',
            $exception->getMessage(),
        );
    }

    public function testNoFormMapper(): void
    {
        $exception = ConditionTypeException::noFormMapper('type');

        self::assertSame(
            'Form mapper for "type" condition type does not exist.',
            $exception->getMessage(),
        );
    }
}
