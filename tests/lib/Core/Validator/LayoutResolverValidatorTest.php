<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Validator;

use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
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

final class LayoutResolverValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry
     */
    private $targetTypeRegistry;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry
     */
    private $conditionTypeRegistry;

    /**
     * @var \Netgen\Layouts\Core\Validator\LayoutResolverValidator
     */
    private $layoutResolverValidator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->conditionTypeRegistry = new ConditionTypeRegistry([new ConditionType1()]);

        $this->layoutResolverValidator = new LayoutResolverValidator(
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry
        );

        $this->layoutResolverValidator->setValidator($this->validator);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::__construct
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateRuleCreateStruct
     * @dataProvider validateRuleCreateStructDataProvider
     */
    public function testValidateRuleCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new RuleCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutResolverValidator->validateRuleCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateRuleUpdateStruct
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
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateRuleMetadataUpdateStruct
     * @dataProvider validateRuleMetadataUpdateStructDataProvider
     */
    public function testValidateRuleMetadataUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new RuleMetadataUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutResolverValidator->validateRuleMetadataUpdateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateTargetCreateStruct
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
            $struct
        );
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutResolverValidator::validateConditionCreateStruct
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
            $struct
        );
    }

    public function validateRuleCreateStructDataProvider(): array
    {
        return [
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], true],
            [['uuid' => Uuid::uuid4(), 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], true],
            [['uuid' => 42, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], false],
            [['uuid' => null, 'layoutId' => null, 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], true],
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], true],
            [['uuid' => null, 'layoutId' => '', 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], false],
            [['uuid' => null, 'layoutId' => [], 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], false],
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => null, 'enabled' => true, 'comment' => 'Comment'], true],
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => '2', 'enabled' => true, 'comment' => 'Comment'], false],
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => false, 'comment' => 'Comment'], true],
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => null, 'comment' => 'Comment'], true],
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => 0, 'comment' => 'Comment'], false],
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => 1, 'comment' => 'Comment'], false],
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => true, 'comment' => null], true],
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => true, 'comment' => ''], true],
            [['uuid' => null, 'layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'priority' => 2, 'enabled' => true, 'comment' => 42], false],
        ];
    }

    public function validateRuleUpdateStructDataProvider(): array
    {
        return [
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'comment' => 'Comment'], true],
            [['layoutId' => null, 'comment' => 'Comment'], true],
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'comment' => 'Comment'], true],
            [['layoutId' => '', 'comment' => 'Comment'], false],
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'comment' => null], true],
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'comment' => ''], true],
            [['layoutId' => Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), 'comment' => 42], false],
        ];
    }

    public function validateRuleMetadataUpdateStructDataProvider(): array
    {
        return [
            [['priority' => -12], true],
            [['priority' => 0], true],
            [['priority' => 12], true],
            [['priority' => null], true],
            [['priority' => '12'], false],
            [['priority' => ''], false],
        ];
    }

    public function validateTargetCreateStructDataProvider(): array
    {
        return [
            [['type' => 'target1', 'value' => 42], true],
            [['type' => 'target1', 'value' => '42'], true],
            [['type' => 'target1', 'value' => [42]], true],
            [['type' => '', 'value' => 42], false],
            [['type' => null, 'value' => 42], false],
            [['type' => 42, 'value' => 42], false],
            [['type' => 'target1', 'value' => null], false],
            [['type' => 'target1', 'value' => ''], false],
            [['type' => 'target1', 'value' => []], false],
        ];
    }

    public function validateTargetUpdateStructDataProvider(): array
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

    public function validateConditionCreateStructDataProvider(): array
    {
        return [
            [['type' => 'condition1', 'value' => 42], true],
            [['type' => 'condition1', 'value' => '42'], true],
            [['type' => 'condition1', 'value' => [42]], true],
            [['type' => '', 'value' => 42], false],
            [['type' => null, 'value' => 42], false],
            [['type' => 42, 'value' => 42], false],
            [['type' => 'condition1', 'value' => null], false],
            [['type' => 'condition1', 'value' => ''], false],
            [['type' => 'condition1', 'value' => []], false],
        ];
    }

    public function validateConditionUpdateStructDataProvider(): array
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
