<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParameterTypeException::class)]
final class ParameterTypeExceptionTest extends TestCase
{
    public function testNoParameterType(): void
    {
        $exception = ParameterTypeException::noParameterType('type');

        self::assertSame(
            'Parameter type with "type" identifier does not exist.',
            $exception->getMessage(),
        );
    }

    public function testNoParameterTypeClass(): void
    {
        $exception = ParameterTypeException::noParameterTypeClass('class');

        self::assertSame(
            'Parameter type with class "class" does not exist.',
            $exception->getMessage(),
        );
    }

    public function testNoFormMapper(): void
    {
        $exception = ParameterTypeException::noFormMapper('type');

        self::assertSame(
            'Form mapper for "type" parameter type does not exist.',
            $exception->getMessage(),
        );
    }

    public function testUnsupportedParameterType(): void
    {
        $exception = ParameterTypeException::unsupportedParameterType('type');

        self::assertSame(
            'Parameter with "type" type is not supported.',
            $exception->getMessage(),
        );
    }
}
