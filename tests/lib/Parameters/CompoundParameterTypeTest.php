<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\Compound\BooleanType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;

final class CompoundParameterTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\CompoundParameterTypeInterface
     */
    private $parameterType;

    public function setUp(): void
    {
        $this->parameterType = new CompoundParameterType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::buildParameters
     */
    public function testBuildParameters(): void
    {
        $parameterBuilderFactory = new ParameterBuilderFactory(new ParameterTypeRegistry());

        $parameterBuilder = $parameterBuilderFactory->createParameterBuilder();
        $this->parameterType->buildParameters($parameterBuilder);

        $this->assertCount(0, $parameterBuilder);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getConstraints
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getValueConstraints
     */
    public function testGetConstraints(): void
    {
        $constraints = $this->parameterType->getConstraints(
            CompoundParameterDefinition::fromArray(
                [
                    'type' => new CompoundParameterType(),
                ]
            ),
            42
        );

        $this->assertCount(1, $constraints);
        $this->assertInstanceOf(Constraints\NotNull::class, $constraints[0]);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getConstraints
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getValueConstraints
     */
    public function testGetConstraintsWithRequiredParameter(): void
    {
        $constraints = $this->parameterType->getConstraints(
            CompoundParameterDefinition::fromArray(
                [
                    'type' => new CompoundParameterType(),
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
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getConstraints
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     * @expectedExceptionMessage Parameter with "compound_boolean" type is not supported
     */
    public function testGetConstraintsThrowsParameterTypeException(): void
    {
        $this->parameterType->getConstraints(
            CompoundParameterDefinition::fromArray(['type' => new BooleanType()]),
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::toHash
     */
    public function testToHash(): void
    {
        $this->assertSame(42, $this->parameterType->toHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::fromHash
     */
    public function testFromHash(): void
    {
        $this->assertSame(42, $this->parameterType->fromHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::isValueEmpty
     */
    public function testIsValueEmpty(): void
    {
        $this->assertTrue($this->parameterType->isValueEmpty(new ParameterDefinition(), null));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::isValueEmpty
     */
    public function testIsValueEmptyReturnsFalse(): void
    {
        $this->assertFalse($this->parameterType->isValueEmpty(new ParameterDefinition(), 42));
    }
}
