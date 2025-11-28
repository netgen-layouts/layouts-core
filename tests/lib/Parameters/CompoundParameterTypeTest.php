<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Netgen\Layouts\Parameters\CompoundParameterType;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\Compound\BooleanType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Tests\Parameters\Stubs\CompoundParameterType as CompoundParameterTypeStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;

#[CoversClass(CompoundParameterType::class)]
final class CompoundParameterTypeTest extends TestCase
{
    private CompoundParameterTypeStub $parameterType;

    protected function setUp(): void
    {
        $this->parameterType = new CompoundParameterTypeStub();
    }

    public function testBuildParameters(): void
    {
        $parameterBuilderFactory = new ParameterBuilderFactory(new ParameterTypeRegistry([]));

        $parameterBuilder = $parameterBuilderFactory->createParameterBuilder();
        $this->parameterType->buildParameters($parameterBuilder);

        self::assertCount(0, $parameterBuilder);
    }

    public function testGetConstraints(): void
    {
        $constraints = $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(
                [
                    'type' => new CompoundParameterTypeStub(),
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
                    'type' => new CompoundParameterTypeStub(),
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
        $this->expectExceptionMessage('Parameter with "compound_boolean" type is not supported');

        $this->parameterType->getConstraints(
            ParameterDefinition::fromArray(
                [
                    'type' => new BooleanType(),
                    'isRequired' => false,
                ],
            ),
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

    public function testIsValueEmpty(): void
    {
        self::assertTrue($this->parameterType->isValueEmpty(new ParameterDefinition(), null));
    }

    public function testIsValueEmptyReturnsFalse(): void
    {
        self::assertFalse($this->parameterType->isValueEmpty(new ParameterDefinition(), 42));
    }
}
