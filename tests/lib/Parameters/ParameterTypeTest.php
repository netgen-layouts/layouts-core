<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterTypeException;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;

final class ParameterTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    private $parameterType;

    public function setUp(): void
    {
        $this->parameterType = new ParameterType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getValueConstraints
     */
    public function testGetConstraints(): void
    {
        $constraints = $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterType(),
                ]
            ),
            42
        );

        self::assertCount(1, $constraints);
        self::assertInstanceOf(Constraints\NotNull::class, $constraints[0]);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getValueConstraints
     */
    public function testGetConstraintsWithRequiredParameter(): void
    {
        $constraints = $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterType(),
                    'isRequired' => true,
                ]
            ),
            42
        );

        self::assertCount(2, $constraints);
        self::assertInstanceOf(Constraints\NotBlank::class, $constraints[0]);
        self::assertInstanceOf(Constraints\NotNull::class, $constraints[1]);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getConstraints
     */
    public function testGetConstraintsThrowsParameterTypeException(): void
    {
        $this->expectException(ParameterTypeException::class);
        $this->expectExceptionMessage('Parameter with "text" type is not supported');

        $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(['type' => new TextType()]),
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::toHash
     */
    public function testToHash(): void
    {
        self::assertSame(42, $this->parameterType->toHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::fromHash
     */
    public function testFromHash(): void
    {
        self::assertSame(42, $this->parameterType->fromHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::export
     */
    public function testExport(): void
    {
        self::assertSame(42, $this->parameterType->export(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::import
     */
    public function testImport(): void
    {
        self::assertSame(42, $this->parameterType->import(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::isValueEmpty
     */
    public function testIsValueEmpty(): void
    {
        self::assertTrue($this->parameterType->isValueEmpty(new ParameterDefinition(), null));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::isValueEmpty
     */
    public function testIsValueEmptyReturnsFalse(): void
    {
        self::assertFalse($this->parameterType->isValueEmpty(new ParameterDefinition(), 42));
    }
}
