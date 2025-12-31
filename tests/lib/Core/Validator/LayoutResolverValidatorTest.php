<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Validator;

use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\Layouts\Core\Validator\LayoutResolverValidator;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\VarExporter\Hydrator;

#[CoversClass(LayoutResolverValidator::class)]
final class LayoutResolverValidatorTest extends TestCase
{
    use ValidatorTestCaseTrait;

    private LayoutResolverValidator $layoutResolverValidator;

    protected function setUp(): void
    {
        $targetTypeRegistry = new TargetTypeRegistry([TargetType1::getType() => new TargetType1(42)]);
        $conditionTypeRegistry = new ConditionTypeRegistry([ConditionType1::getType() => new ConditionType1()]);

        $this->layoutResolverValidator = new LayoutResolverValidator(
            $targetTypeRegistry,
            $conditionTypeRegistry,
        );

        $this->layoutResolverValidator->setValidator($this->createValidator());
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('validateRuleUpdateStructDataProvider')]
    public function testValidateRuleUpdateStruct(array $params, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $struct = new RuleUpdateStruct();
        Hydrator::hydrate($struct, $params);

        $this->layoutResolverValidator->validateRuleUpdateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('validateRuleGroupCreateStructDataProvider')]
    public function testValidateRuleGroupCreateStruct(array $params, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $struct = new RuleGroupCreateStruct();
        Hydrator::hydrate($struct, $params);

        $this->layoutResolverValidator->validateRuleGroupCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('validateRuleGroupUpdateStructDataProvider')]
    public function testValidateRuleGroupUpdateStruct(array $params, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $struct = new RuleGroupUpdateStruct();
        Hydrator::hydrate($struct, $params);

        $this->layoutResolverValidator->validateRuleGroupUpdateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('validateTargetCreateStructDataProvider')]
    public function testValidateTargetCreateStruct(array $params, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $struct = new TargetCreateStruct();
        Hydrator::hydrate($struct, $params);

        $this->layoutResolverValidator->validateTargetCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('validateTargetUpdateStructDataProvider')]
    public function testValidateTargetUpdateStruct(array $params, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $struct = new TargetUpdateStruct();
        Hydrator::hydrate($struct, $params);

        $this->layoutResolverValidator->validateTargetUpdateStruct(
            Target::fromArray(['targetType' => new TargetType1()]),
            $struct,
        );
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('validateConditionCreateStructDataProvider')]
    public function testValidateConditionCreateStruct(array $params, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $struct = new ConditionCreateStruct();
        Hydrator::hydrate($struct, $params);

        $this->layoutResolverValidator->validateConditionCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('validateConditionUpdateStructDataProvider')]
    public function testValidateConditionUpdateStruct(array $params, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $struct = new ConditionUpdateStruct();
        Hydrator::hydrate($struct, $params);

        $this->layoutResolverValidator->validateConditionUpdateStruct(
            RuleCondition::fromArray(['conditionType' => new ConditionType1()]),
            $struct,
        );
    }

    /**
     * @return iterable<mixed>
     */
    public static function validateRuleUpdateStructDataProvider(): iterable
    {
        return [
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'description' => 'Description'], true],
            [['layoutId' => null, 'description' => 'Description'], true],
            [['layoutId' => false, 'description' => 'Description'], true],
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'description' => 'Description'], true],
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'description' => null], true],
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'description' => ''], true],
        ];
    }

    /**
     * @return iterable<mixed>
     */
    public static function validateRuleGroupCreateStructDataProvider(): iterable
    {
        return [
            [['uuid' => null, 'name' => 'Name', 'priority' => 2, 'isEnabled' => true, 'description' => 'Description'], true],
            [['uuid' => Uuid::v7(), 'name' => 'Name', 'priority' => 2, 'isEnabled' => true, 'description' => 'Description'], true],
            [['uuid' => null, 'name' => 'Name', 'priority' => 2, 'isEnabled' => true, 'description' => 'Description'], true],
            [['uuid' => null, 'name' => '', 'priority' => 2, 'isEnabled' => true, 'description' => 'Description'], false],
            [['uuid' => null, 'name' => 'Name', 'priority' => 2, 'isEnabled' => false, 'description' => 'Description'], true],
            [['uuid' => null, 'name' => 'Name', 'priority' => 2, 'isEnabled' => true, 'description' => ''], true],
        ];
    }

    /**
     * @return iterable<mixed>
     */
    public static function validateRuleGroupUpdateStructDataProvider(): iterable
    {
        return [
            [['name' => 'Name', 'description' => 'Description'], true],
            [['name' => null, 'description' => 'Description'], true],
            [['name' => '', 'description' => 'Description'], false],
            [['name' => 'Name', 'description' => 'Description'], true],
            [['name' => 'Name', 'description' => null], true],
            [['name' => 'Name', 'description' => ''], true],
        ];
    }

    /**
     * @return iterable<mixed>
     */
    public static function validateTargetCreateStructDataProvider(): iterable
    {
        return [
            [['type' => 'target1', 'value' => 42], true],
            [['type' => 'target1', 'value' => '42'], true],
            [['type' => 'target1', 'value' => ''], false],
        ];
    }

    /**
     * @return iterable<mixed>
     */
    public static function validateTargetUpdateStructDataProvider(): iterable
    {
        return [
            [['value' => 42], true],
            [['value' => '42'], true],
            [['value' => ''], false],
        ];
    }

    /**
     * @return iterable<mixed>
     */
    public static function validateConditionCreateStructDataProvider(): iterable
    {
        return [
            [['type' => 'condition1', 'value' => 42], true],
            [['type' => 'condition1', 'value' => '42'], true],
            [['type' => 'condition1', 'value' => [42]], true],
            [['type' => 'condition1', 'value' => ''], false],
            [['type' => 'condition1', 'value' => []], false],
        ];
    }

    /**
     * @return iterable<mixed>
     */
    public static function validateConditionUpdateStructDataProvider(): iterable
    {
        return [
            [['value' => 42], true],
            [['value' => '42'], true],
            [['value' => [42]], true],
            [['value' => ''], false],
            [['value' => []], false],
        ];
    }
}
