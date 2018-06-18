<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

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
            new ParameterDefinition(
                [
                    'type' => new ParameterType(),
                ]
            ),
            42
        );

        $this->assertCount(1, $constraints);
        $this->assertInstanceOf(Constraints\NotNull::class, $constraints[0]);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getValueConstraints
     */
    public function testGetConstraintsWithRequiredParameter(): void
    {
        $constraints = $this->parameterType->getConstraints(
            new ParameterDefinition(
                [
                    'type' => new ParameterType(),
                    'isRequired' => true,
                ]
            ),
            42
        );

        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Constraints\NotBlank::class, $constraints[0]);
        $this->assertInstanceOf(Constraints\NotNull::class, $constraints[1]);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getConstraints
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     * @expectedExceptionMessage Parameter with "text" type is not supported
     */
    public function testGetConstraintsThrowsParameterTypeException(): void
    {
        $this->parameterType->getConstraints(
            new ParameterDefinition(['type' => new TextType()]),
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::toHash
     */
    public function testToHash(): void
    {
        $this->assertSame(42, $this->parameterType->toHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::fromHash
     */
    public function testFromHash(): void
    {
        $this->assertSame(42, $this->parameterType->fromHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::export
     */
    public function testExport(): void
    {
        $this->assertSame(42, $this->parameterType->export(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::import
     */
    public function testImport(): void
    {
        $this->assertSame(42, $this->parameterType->import(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::isValueEmpty
     */
    public function testIsValueEmpty(): void
    {
        $this->assertTrue($this->parameterType->isValueEmpty(new ParameterDefinition(), null));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::isValueEmpty
     */
    public function testIsValueEmptyReturnsFalse(): void
    {
        $this->assertFalse($this->parameterType->isValueEmpty(new ParameterDefinition(), 42));
    }
}
