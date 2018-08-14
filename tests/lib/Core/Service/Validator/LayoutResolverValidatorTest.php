<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType1;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Netgen\BlockManager\Utils\Hydrator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class LayoutResolverValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    private $conditionTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator
     */
    private $layoutResolverValidator;

    public function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->targetTypeRegistry = new TargetTypeRegistry(new TargetType1(42));

        $this->conditionTypeRegistry = new ConditionTypeRegistry(new ConditionType1());

        $this->layoutResolverValidator = new LayoutResolverValidator(
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry
        );

        $this->layoutResolverValidator->setValidator($this->validator);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateRuleCreateStruct
     * @dataProvider validateRuleCreateStructProvider
     */
    public function testValidateRuleCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new RuleCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        self::assertTrue(true);

        $this->layoutResolverValidator->validateRuleCreateStruct($struct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateRuleUpdateStruct
     * @dataProvider validateRuleUpdateStructProvider
     */
    public function testValidateRuleUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new RuleUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        self::assertTrue(true);

        $this->layoutResolverValidator->validateRuleUpdateStruct($struct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateRuleMetadataUpdateStruct
     * @dataProvider validateRuleMetadataUpdateStructProvider
     */
    public function testValidateRuleMetadataUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new RuleMetadataUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        self::assertTrue(true);

        $this->layoutResolverValidator->validateRuleMetadataUpdateStruct($struct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateTargetCreateStruct
     * @dataProvider validateTargetCreateStructProvider
     */
    public function testValidateTargetCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new TargetCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        self::assertTrue(true);

        $this->layoutResolverValidator->validateTargetCreateStruct($struct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateTargetUpdateStruct
     * @dataProvider validateTargetUpdateStructProvider
     */
    public function testValidateTargetUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new TargetUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        self::assertTrue(true);

        $this->layoutResolverValidator->validateTargetUpdateStruct(
            Target::fromArray(['targetType' => new TargetType1()]),
            $struct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateConditionCreateStruct
     * @dataProvider validateConditionCreateStructProvider
     */
    public function testValidateConditionCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new ConditionCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        self::assertTrue(true);

        $this->layoutResolverValidator->validateConditionCreateStruct($struct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateConditionUpdateStruct
     * @dataProvider validateConditionUpdateStructProvider
     */
    public function testValidateConditionUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new ConditionUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Fake assertion to fix coverage on tests which do not perform assertions
        self::assertTrue(true);

        $this->layoutResolverValidator->validateConditionUpdateStruct(
            Condition::fromArray(['conditionType' => new ConditionType1()]),
            $struct
        );
    }

    public function validateRuleCreateStructProvider(): array
    {
        return [
            [['layoutId' => 12, 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], true],
            [['layoutId' => null, 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], true],
            [['layoutId' => '12', 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], true],
            [['layoutId' => '', 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], false],
            [['layoutId' => [], 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'], false],
            [['layoutId' => 12, 'priority' => null, 'enabled' => true, 'comment' => 'Comment'], true],
            [['layoutId' => 12, 'priority' => '2', 'enabled' => true, 'comment' => 'Comment'], false],
            [['layoutId' => 12, 'priority' => 2, 'enabled' => false, 'comment' => 'Comment'], true],
            [['layoutId' => 12, 'priority' => 2, 'enabled' => null, 'comment' => 'Comment'], true],
            [['layoutId' => 12, 'priority' => 2, 'enabled' => 0, 'comment' => 'Comment'], false],
            [['layoutId' => 12, 'priority' => 2, 'enabled' => 1, 'comment' => 'Comment'], false],
            [['layoutId' => 12, 'priority' => 2, 'enabled' => true, 'comment' => null], true],
            [['layoutId' => 12, 'priority' => 2, 'enabled' => true, 'comment' => ''], true],
            [['layoutId' => 12, 'priority' => 2, 'enabled' => true, 'comment' => 42], false],
        ];
    }

    public function validateRuleUpdateStructProvider(): array
    {
        return [
            [['layoutId' => 12, 'comment' => 'Comment'], true],
            [['layoutId' => null, 'comment' => 'Comment'], true],
            [['layoutId' => '12', 'comment' => 'Comment'], true],
            [['layoutId' => '', 'comment' => 'Comment'], false],
            [['layoutId' => 12, 'comment' => null], true],
            [['layoutId' => 12, 'comment' => ''], true],
            [['layoutId' => 12, 'comment' => 42], false],
        ];
    }

    public function validateRuleMetadataUpdateStructProvider(): array
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

    public function validateTargetCreateStructProvider(): array
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

    public function validateTargetUpdateStructProvider(): array
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

    public function validateConditionCreateStructProvider(): array
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

    public function validateConditionUpdateStructProvider(): array
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
