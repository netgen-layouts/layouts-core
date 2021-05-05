<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use PHPUnit\Framework\TestCase;

final class ParameterExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Parameters\ParameterException::noParameter
     */
    public function testNoParameter(): void
    {
        $exception = ParameterException::noParameter('param');

        self::assertSame(
            'Parameter with "param" name does not exist.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\Parameters\ParameterException::noParameterDefinition
     */
    public function testNoParameterDefinition(): void
    {
        $exception = ParameterException::noParameterDefinition('param');

        self::assertSame(
            'Parameter definition with "param" name does not exist.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\Parameters\ParameterException::noOption
     */
    public function testNoOption(): void
    {
        $exception = ParameterException::noOption('opt');

        self::assertSame(
            'Option "opt" does not exist in the parameter definition.',
            $exception->getMessage(),
        );
    }
}
