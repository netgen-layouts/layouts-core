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
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use Netgen\Layouts\Utils\Hydrator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class LayoutResolverValidatorTest extends TestCase
{
    private ValidatorInterface $validator;

    private TargetTypeRegistry $targetTypeRegistry;

    private ConditionTypeRegistry $conditionTypeRegistry;

    private LayoutResolverValidator $layoutResolverValidator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->conditionTypeRegistry = new ConditionTypeRegistry([new ConditionType1()]);

        $this->layoutResolverValidator = new LayoutResolverValidator(
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry,
        );

        $this->layoutResolverValidator->setValidator($this->validator);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateRuleUpdateStruct
     *
     * @dataProvider validateRuleUpdateStructDataProvider
     */
    public function testValidateRuleUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new RuleUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutResolverValidator->validateRuleUpdateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateRuleGroupCreateStruct
     *
     * @dataProvider validateRuleGroupCreateStructDataProvider
     */
    public function testValidateRuleGroupCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new RuleGroupCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutResolverValidator->validateRuleGroupCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateRuleGroupUpdateStruct
     *
     * @dataProvider validateRuleGroupUpdateStructDataProvider
     */
    public function testValidateRuleGroupUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new RuleGroupUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutResolverValidator->validateRuleGroupUpdateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateTargetCreateStruct
     *
     * @dataProvider validateTargetCreateStructDataProvider
     */
    public function testValidateTargetCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new TargetCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutResolverValidator->validateTargetCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateTargetUpdateStruct
     *
     * @dataProvider validateTargetUpdateStructDataProvider
     */
    public function testValidateTargetUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new TargetUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutResolverValidator->validateTargetUpdateStruct(
            Target::fromArray(['targetType' => new TargetType1()]),
            $struct,
        );
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateConditionCreateStruct
     *
     * @dataProvider validateConditionCreateStructDataProvider
     */
    public function testValidateConditionCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new ConditionCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutResolverValidator->validateConditionCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateConditionUpdateStruct
     *
     * @dataProvider validateConditionUpdateStructDataProvider
     */
    public function testValidateConditionUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new ConditionUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutResolverValidator->validateConditionUpdateStruct(
            RuleCondition::fromArray(['conditionType' => new ConditionType1()]),
            $struct,
        );
    }

    public static function validateRuleUpdateStructDataProvider(): iterable
    {
        return [
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'description' => 'Description'], true],
            [['layoutId' => null, 'description' => 'Description'], true],
            [['layoutId' => false, 'description' => 'Description'], true],
            [['layoutId' => true, 'description' => 'Description'], false],
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'description' => 'Description'], true],
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'description' => null], true],
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'description' => ''], true],
        ];
    }

    public static function validateRuleGroupCreateStructDataProvider(): iterable
    {
        return [
            [['uuid' => null, 'name' => 'Name', 'priority' => 2, 'enabled' => true, 'description' => 'Description'], true],
            [['uuid' => Uuid::uuid4(), 'name' => 'Name', 'priority' => 2, 'enabled' => true, 'description' => 'Description'], true],
            [['uuid' => null, 'name' => 'Name', 'priority' => 2, 'enabled' => true, 'description' => 'Description'], true],
            [['uuid' => null, 'name' => '', 'priority' => 2, 'enabled' => true, 'description' => 'Description'], false],
            [['uuid' => null, 'name' => 'Name', 'priority' => 2, 'enabled' => false, 'description' => 'Description'], true],
            [['uuid' => null, 'name' => 'Name', 'priority' => 2, 'enabled' => true, 'description' => ''], true],
        ];
    }

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

    public static function validateTargetCreateStructDataProvider(): iterable
    {
        return [
            [['type' => 'target1', 'value' => 42], true],
            [['type' => 'target1', 'value' => '42'], true],
            [['type' => 'target1', 'value' => [42]], true],
            [['type' => 'target1', 'value' => null], false],
            [['type' => 'target1', 'value' => ''], false],
            [['type' => 'target1', 'value' => []], false],
        ];
    }

    public static function validateTargetUpdateStructDataProvider(): iterable
    {
        return [
            [['value' => 42], true],
            [['value' => '42'], true],
            [['value' => [42]], true],
            [['value' => null], false],
            [['value' => ''], false],
            [['value' => []], false],
        ];
    }

    public static function validateConditionCreateStructDataProvider(): iterable
    {
        return [
            [['type' => 'condition1', 'value' => 42], true],
            [['type' => 'condition1', 'value' => '42'], true],
            [['type' => 'condition1', 'value' => [42]], true],
            [['type' => 'condition1', 'value' => null], false],
            [['type' => 'condition1', 'value' => ''], false],
            [['type' => 'condition1', 'value' => []], false],
        ];
    }

    public static function validateConditionUpdateStructDataProvider(): iterable
    {
        return [
            [['value' => 42], true],
            [['value' => '42'], true],
            [['value' => [42]], true],
            [['value' => null], false],
            [['value' => ''], false],
            [['value' => []], false],
        ];
    }
}
