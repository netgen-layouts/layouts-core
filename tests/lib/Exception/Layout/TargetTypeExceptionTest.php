<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Layout;

use Netgen\BlockManager\Exception\Layout\TargetTypeException;
use PHPUnit\Framework\TestCase;

final class TargetTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Layout\TargetTypeException::noTargetType
     */
    public function testNoTargetType(): void
    {
        $exception = TargetTypeException::noTargetType('type');

        $this->assertEquals(
            'Target type "type" does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Layout\TargetTypeException::noFormMapper
     */
    public function testNoFormMapper(): void
    {
        $exception = TargetTypeException::noFormMapper('type');

        $this->assertEquals(
            'Form mapper for "type" target type does not exist.',
            $exception->getMessage()
        );
    }
}
