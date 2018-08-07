<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Layout;

use Netgen\BlockManager\Exception\Layout\ConditionTypeException;
use PHPUnit\Framework\TestCase;

final class ConditionTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Layout\ConditionTypeException::noConditionType
     */
    public function testNoConditionType(): void
    {
        $exception = ConditionTypeException::noConditionType('type');

        self::assertSame(
            'Condition type "type" does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Layout\ConditionTypeException::noFormMapper
     */
    public function testNoFormMapper(): void
    {
        $exception = ConditionTypeException::noFormMapper('type');

        self::assertSame(
            'Form mapper for "type" condition type does not exist.',
            $exception->getMessage()
        );
    }
}
