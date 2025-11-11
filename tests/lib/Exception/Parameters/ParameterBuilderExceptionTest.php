<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterBuilderException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParameterBuilderException::class)]
final class ParameterBuilderExceptionTest extends TestCase
{
    public function testNoParameter(): void
    {
        $exception = ParameterBuilderException::noParameter('param');

        self::assertSame(
            'Parameter with "param" name does not exist in the builder.',
            $exception->getMessage(),
        );
    }

    public function testNoOption(): void
    {
        $exception = ParameterBuilderException::noOption('opt', 'param');

        self::assertSame(
            'Option "opt" does not exist in the builder for "param" parameter.',
            $exception->getMessage(),
        );
    }

    public function testNoOptionWithoutParameterName(): void
    {
        $exception = ParameterBuilderException::noOption('opt');

        self::assertSame(
            'Option "opt" does not exist in the builder.',
            $exception->getMessage(),
        );
    }

    public function testSubCompound(): void
    {
        $exception = ParameterBuilderException::subCompound();

        self::assertSame(
            'Compound parameters cannot be added to compound parameters.',
            $exception->getMessage(),
        );
    }

    public function testNonCompound(): void
    {
        $exception = ParameterBuilderException::nonCompound();

        self::assertSame(
            'Parameters cannot be added to non-compound parameters.',
            $exception->getMessage(),
        );
    }

    public function testInvalidConstraints(): void
    {
        $exception = ParameterBuilderException::invalidConstraints();

        self::assertSame(
            'Parameter constraints need to be either a Symfony constraint or a closure.',
            $exception->getMessage(),
        );
    }
}
