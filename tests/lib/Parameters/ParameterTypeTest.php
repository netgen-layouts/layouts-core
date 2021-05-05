<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\TextType;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;

final class ParameterTypeTest extends TestCase
{
    private ParameterType $parameterType;

    protected function setUp(): void
    {
        $this->parameterType = new ParameterType();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType::getConstraints
     * @covers \Netgen\Layouts\Parameters\ParameterType::getRequiredConstraints
     * @covers \Netgen\Layouts\Parameters\ParameterType::getValueConstraints
     */
    public function testGetConstraints(): void
    {
        $constraints = $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterType(),
                    'isRequired' => false,
                ],
            ),
            42,
        );

        self::assertCount(1, $constraints);
        self::assertInstanceOf(Constraints\NotNull::class, $constraints[0]);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType::getConstraints
     * @covers \Netgen\Layouts\Parameters\ParameterType::getRequiredConstraints
     * @covers \Netgen\Layouts\Parameters\ParameterType::getValueConstraints
     */
    public function testGetConstraintsWithRequiredParameter(): void
    {
        $constraints = $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterType(),
                    'isRequired' => true,
                ],
            ),
            42,
        );

        self::assertCount(2, $constraints);
        self::assertInstanceOf(Constraints\NotBlank::class, $constraints[0]);
        self::assertInstanceOf(Constraints\NotNull::class, $constraints[1]);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType::getConstraints
     */
    public function testGetConstraintsThrowsParameterTypeException(): void
    {
        $this->expectException(ParameterTypeException::class);
        $this->expectExceptionMessage('Parameter with "text" type is not supported');

        $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(['type' => new TextType()]),
            42,
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType::toHash
     */
    public function testToHash(): void
    {
        self::assertSame(42, $this->parameterType->toHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType::fromHash
     */
    public function testFromHash(): void
    {
        self::assertSame(42, $this->parameterType->fromHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType::export
     */
    public function testExport(): void
    {
        self::assertSame(42, $this->parameterType->export(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType::import
     */
    public function testImport(): void
    {
        self::assertSame(42, $this->parameterType->import(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType::isValueEmpty
     */
    public function testIsValueEmpty(): void
    {
        self::assertTrue($this->parameterType->isValueEmpty(new ParameterDefinition(), null));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType::isValueEmpty
     */
    public function testIsValueEmptyReturnsFalse(): void
    {
        self::assertFalse($this->parameterType->isValueEmpty(new ParameterDefinition(), 42));
    }
}
