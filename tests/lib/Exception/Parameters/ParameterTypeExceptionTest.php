<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterTypeException;
use PHPUnit\Framework\TestCase;

final class ParameterTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterTypeException::noParameterType
     */
    public function testNoParameterType(): void
    {
        $exception = ParameterTypeException::noParameterType('type');

        self::assertSame(
            'Parameter type with "type" identifier does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterTypeException::noParameterTypeClass
     */
    public function testNoParameterTypeClass(): void
    {
        $exception = ParameterTypeException::noParameterTypeClass('class');

        self::assertSame(
            'Parameter type with class "class" does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterTypeException::noFormMapper
     */
    public function testNoFormMapper(): void
    {
        $exception = ParameterTypeException::noFormMapper('type');

        self::assertSame(
            'Form mapper for "type" parameter type does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterTypeException::unsupportedParameterType
     */
    public function testUnsupportedParameterType(): void
    {
        $exception = ParameterTypeException::unsupportedParameterType('type');

        self::assertSame(
            'Parameter with "type" type is not supported.',
            $exception->getMessage()
        );
    }
}
