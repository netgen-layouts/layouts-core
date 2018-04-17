<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
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

    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->targetTypeRegistry = new TargetTypeRegistry();
        $this->targetTypeRegistry->addTargetType(new TargetType('target', 42));

        $this->conditionTypeRegistry = new ConditionTypeRegistry();
        $this->conditionTypeRegistry->addConditionType(new ConditionType('condition'));

        $this->layoutResolverValidator = new LayoutResolverValidator(
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry
        );

        $this->layoutResolverValidator->setValidator($this->validator);
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateRuleCreateStruct
     * @dataProvider validateRuleCreateStructProvider
     */
    public function testValidateRuleCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutResolverValidator->validateRuleCreateStruct(new RuleCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateRuleUpdateStruct
     * @dataProvider validateRuleUpdateStructProvider
     */
    public function testValidateRuleUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutResolverValidator->validateRuleUpdateStruct(new RuleUpdateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateRuleMetadataUpdateStruct
     * @dataProvider validateRuleMetadataUpdateStructProvider
     */
    public function testValidateRuleMetadataUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutResolverValidator->validateRuleMetadataUpdateStruct(
            new RuleMetadataUpdateStruct($params)
        );
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateTargetCreateStruct
     * @dataProvider validateTargetCreateStructProvider
     */
    public function testValidateTargetCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutResolverValidator->validateTargetCreateStruct(new TargetCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateTargetUpdateStruct
     * @dataProvider validateTargetUpdateStructProvider
     */
    public function testValidateTargetUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutResolverValidator->validateTargetUpdateStruct(
            new Target(['targetType' => new TargetType('target')]),
            new TargetUpdateStruct($params)
        );
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateConditionCreateStruct
     * @dataProvider validateConditionCreateStructProvider
     */
    public function testValidateConditionCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutResolverValidator->validateConditionCreateStruct(new ConditionCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateConditionUpdateStruct
     * @dataProvider validateConditionUpdateStructProvider
     */
    public function testValidateConditionUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutResolverValidator->validateConditionUpdateStruct(
            new Condition(['conditionType' => new ConditionType('condition')]),
            new ConditionUpdateStruct($params)
        );
    }

    public function validateRuleCreateStructProvider()
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

    public function validateRuleUpdateStructProvider()
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

    public function validateRuleMetadataUpdateStructProvider()
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

    public function validateTargetCreateStructProvider()
    {
        return [
            [['type' => 'target', 'value' => 42], true],
            [['type' => 'target', 'value' => '42'], true],
            [['type' => 'target', 'value' => [42]], true],
            [['type' => '', 'value' => 42], false],
            [['type' => null, 'value' => 42], false],
            [['type' => 42, 'value' => 42], false],
            [['type' => 'target', 'value' => null], false],
            [['type' => 'target', 'value' => ''], false],
            [['type' => 'target', 'value' => []], false],
        ];
    }

    public function validateTargetUpdateStructProvider()
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

    public function validateConditionCreateStructProvider()
    {
        return [
            [['type' => 'condition', 'value' => 42], true],
            [['type' => 'condition', 'value' => '42'], true],
            [['type' => 'condition', 'value' => [42]], true],
            [['type' => '', 'value' => 42], false],
            [['type' => null, 'value' => 42], false],
            [['type' => 42, 'value' => 42], false],
            [['type' => 'condition', 'value' => null], false],
            [['type' => 'condition', 'value' => ''], false],
            [['type' => 'condition', 'value' => []], false],
        ];
    }

    public function validateConditionUpdateStructProvider()
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
