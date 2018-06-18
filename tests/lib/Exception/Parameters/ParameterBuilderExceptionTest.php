<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterBuilderException;
use PHPUnit\Framework\TestCase;

final class ParameterBuilderExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException::noParameter
     */
    public function testNoParameter(): void
    {
        $exception = ParameterBuilderException::noParameter('param');

        $this->assertSame(
            'Parameter with "param" name does not exist in the builder.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException::noOption
     */
    public function testNoOption(): void
    {
        $exception = ParameterBuilderException::noOption('opt', 'param');

        $this->assertSame(
            'Option "opt" does not exist in the builder for "param" parameter.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException::subCompound
     */
    public function testSubCompound(): void
    {
        $exception = ParameterBuilderException::subCompound();

        $this->assertSame(
            'Compound parameters cannot be added to compound parameters.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException::nonCompound
     */
    public function testNonCompound(): void
    {
        $exception = ParameterBuilderException::nonCompound();

        $this->assertSame(
            'Parameters cannot be added to non-compound parameters.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException::invalidConstraints
     */
    public function testInvalidConstraints(): void
    {
        $exception = ParameterBuilderException::invalidConstraints();

        $this->assertSame(
            'Parameter constraints need to be either a Symfony constraint or a closure.',
            $exception->getMessage()
        );
    }
}
