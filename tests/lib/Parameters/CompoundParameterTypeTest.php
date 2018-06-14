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
        $this->assertEquals(
            [new Constraints\NotNull()],
            $this->parameterType->getConstraints(
                new CompoundParameterDefinition(
                    [
                        'type' => new CompoundParameterType(),
                    ]
                ),
                42
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getConstraints
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getValueConstraints
     */
    public function testGetConstraintsWithRequiredParameter(): void
    {
        $this->assertEquals(
            [new Constraints\NotBlank(), new Constraints\NotNull()],
            $this->parameterType->getConstraints(
                new CompoundParameterDefinition(
                    [
                        'type' => new CompoundParameterType(),
                        'isRequired' => true,
                    ]
                ),
                42
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getConstraints
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     * @expectedExceptionMessage Parameter with "compound_boolean" type is not supported
     */
    public function testGetConstraintsThrowsParameterTypeException(): void
    {
        $this->parameterType->getConstraints(
            new CompoundParameterDefinition(['type' => new BooleanType()]),
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::toHash
     */
    public function testToHash(): void
    {
        $this->assertEquals(42, $this->parameterType->toHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::fromHash
     */
    public function testFromHash(): void
    {
        $this->assertEquals(42, $this->parameterType->fromHash(new ParameterDefinition(), 42));
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
