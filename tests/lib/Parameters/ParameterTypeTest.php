<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\ParameterType\TextType;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterType as ParameterTypeStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;

#[CoversClass(ParameterType::class)]
final class ParameterTypeTest extends TestCase
{
    private ParameterTypeStub $parameterType;

    protected function setUp(): void
    {
        $this->parameterType = new ParameterTypeStub();
    }

    public function testGetConstraints(): void
    {
        $constraints = $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterTypeStub(),
                    'isRequired' => false,
                ],
            ),
            42,
        );

        self::assertCount(1, $constraints);
        self::assertInstanceOf(Constraints\NotNull::class, $constraints[0]);
    }

    public function testGetConstraintsWithRequiredParameter(): void
    {
        $constraints = $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterTypeStub(),
                    'isRequired' => true,
                ],
            ),
            42,
        );

        self::assertCount(2, $constraints);
        self::assertInstanceOf(Constraints\NotBlank::class, $constraints[0]);
        self::assertInstanceOf(Constraints\NotNull::class, $constraints[1]);
    }

    public function testGetConstraintsThrowsParameterTypeException(): void
    {
        $this->expectException(ParameterTypeException::class);
        $this->expectExceptionMessage('Parameter with "text" type is not supported');

        $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(['type' => new TextType()]),
            42,
        );
    }

    public function testToHash(): void
    {
        self::assertSame(42, $this->parameterType->toHash(new ParameterDefinition(), 42));
    }

    public function testFromHash(): void
    {
        self::assertSame(42, $this->parameterType->fromHash(new ParameterDefinition(), 42));
    }

    public function testExport(): void
    {
        self::assertSame(42, $this->parameterType->export(new ParameterDefinition(), 42));
    }

    public function testImport(): void
    {
        self::assertSame(42, $this->parameterType->import(new ParameterDefinition(), 42));
    }

    public function testIsValueEmpty(): void
    {
        self::assertTrue($this->parameterType->isValueEmpty(new ParameterDefinition(), null));
    }

    public function testIsValueEmptyReturnsFalse(): void
    {
        self::assertFalse($this->parameterType->isValueEmpty(new ParameterDefinition(), 42));
    }
}
