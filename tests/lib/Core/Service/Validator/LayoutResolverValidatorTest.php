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
use Netgen\BlockManager\Exception\ValidationFailedException;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class LayoutResolverValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    protected $conditionTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator
     */
    protected $layoutResolverValidator;

    /**
     * Sets up the test.
     */
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
     * @doesNotPerformAssertions
     */
    public function testValidateRuleCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->layoutResolverValidator->validateRuleCreateStruct(new RuleCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateRuleUpdateStruct
     * @dataProvider validateRuleUpdateStructProvider
     * @doesNotPerformAssertions
     */
    public function testValidateRuleUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->layoutResolverValidator->validateRuleUpdateStruct(new RuleUpdateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateRuleMetadataUpdateStruct
     * @dataProvider validateRuleMetadataUpdateStructProvider
     * @doesNotPerformAssertions
     */
    public function testValidateRuleMetadataUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

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
     * @doesNotPerformAssertions
     */
    public function testValidateTargetCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->layoutResolverValidator->validateTargetCreateStruct(new TargetCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateTargetUpdateStruct
     * @dataProvider validateTargetUpdateStructProvider
     * @doesNotPerformAssertions
     */
    public function testValidateTargetUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->layoutResolverValidator->validateTargetUpdateStruct(
            new Target(array('targetType' => new TargetType('target'))),
            new TargetUpdateStruct($params)
        );
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateConditionCreateStruct
     * @dataProvider validateConditionCreateStructProvider
     * @doesNotPerformAssertions
     */
    public function testValidateConditionCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->layoutResolverValidator->validateConditionCreateStruct(new ConditionCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateConditionUpdateStruct
     * @dataProvider validateConditionUpdateStructProvider
     * @doesNotPerformAssertions
     */
    public function testValidateConditionUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->layoutResolverValidator->validateConditionUpdateStruct(
            new Condition(array('conditionType' => new ConditionType('condition'))),
            new ConditionUpdateStruct($params)
        );
    }

    public function validateRuleCreateStructProvider()
    {
        return array(
            array(array('layoutId' => 12, 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'), true),
            array(array('layoutId' => null, 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'), true),
            array(array('layoutId' => '12', 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'), true),
            array(array('layoutId' => '', 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'), false),
            array(array('layoutId' => array(), 'priority' => 2, 'enabled' => true, 'comment' => 'Comment'), false),
            array(array('layoutId' => 12, 'priority' => null, 'enabled' => true, 'comment' => 'Comment'), true),
            array(array('layoutId' => 12, 'priority' => '2', 'enabled' => true, 'comment' => 'Comment'), false),
            array(array('layoutId' => 12, 'priority' => 2, 'enabled' => false, 'comment' => 'Comment'), true),
            array(array('layoutId' => 12, 'priority' => 2, 'enabled' => null, 'comment' => 'Comment'), true),
            array(array('layoutId' => 12, 'priority' => 2, 'enabled' => 0, 'comment' => 'Comment'), false),
            array(array('layoutId' => 12, 'priority' => 2, 'enabled' => 1, 'comment' => 'Comment'), false),
            array(array('layoutId' => 12, 'priority' => 2, 'enabled' => true, 'comment' => null), true),
            array(array('layoutId' => 12, 'priority' => 2, 'enabled' => true, 'comment' => ''), true),
            array(array('layoutId' => 12, 'priority' => 2, 'enabled' => true, 'comment' => 42), false),
        );
    }

    public function validateRuleUpdateStructProvider()
    {
        return array(
            array(array('layoutId' => 12, 'comment' => 'Comment'), true),
            array(array('layoutId' => null, 'comment' => 'Comment'), true),
            array(array('layoutId' => '12', 'comment' => 'Comment'), true),
            array(array('layoutId' => '', 'comment' => 'Comment'), false),
            array(array('layoutId' => 12, 'comment' => null), true),
            array(array('layoutId' => 12, 'comment' => ''), true),
            array(array('layoutId' => 12, 'comment' => 42), false),
        );
    }

    public function validateRuleMetadataUpdateStructProvider()
    {
        return array(
            array(array('priority' => -12), true),
            array(array('priority' => 0), true),
            array(array('priority' => 12), true),
            array(array('priority' => null), true),
            array(array('priority' => '12'), false),
            array(array('priority' => ''), false),
        );
    }

    public function validateTargetCreateStructProvider()
    {
        return array(
            array(array('type' => 'target', 'value' => 42), true),
            array(array('type' => 'target', 'value' => '42'), true),
            array(array('type' => 'target', 'value' => array(42)), true),
            array(array('type' => '', 'value' => 42), false),
            array(array('type' => null, 'value' => 42), false),
            array(array('type' => 42, 'value' => 42), false),
            array(array('type' => 'target', 'value' => null), false),
            array(array('type' => 'target', 'value' => ''), false),
            array(array('type' => 'target', 'value' => array()), false),
        );
    }

    public function validateTargetUpdateStructProvider()
    {
        return array(
            array(array('value' => 42), true),
            array(array('value' => '42'), true),
            array(array('value' => array(42)), true),
            array(array('value' => null), false),
            array(array('value' => ''), false),
            array(array('value' => array()), false),
        );
    }

    public function validateConditionCreateStructProvider()
    {
        return array(
            array(array('type' => 'condition', 'value' => 42), true),
            array(array('type' => 'condition', 'value' => '42'), true),
            array(array('type' => 'condition', 'value' => array(42)), true),
            array(array('type' => '', 'value' => 42), false),
            array(array('type' => null, 'value' => 42), false),
            array(array('type' => 42, 'value' => 42), false),
            array(array('type' => 'condition', 'value' => null), false),
            array(array('type' => 'condition', 'value' => ''), false),
            array(array('type' => 'condition', 'value' => array()), false),
        );
    }

    public function validateConditionUpdateStructProvider()
    {
        return array(
            array(array('value' => 42), true),
            array(array('value' => '42'), true),
            array(array('value' => array(42)), true),
            array(array('value' => null), false),
            array(array('value' => ''), false),
            array(array('value' => array()), false),
        );
    }
}
