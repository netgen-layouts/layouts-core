<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\Validator\ValidatorFactory;
use Symfony\Component\Validator\Validation;

class LayoutResolverValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

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
            ->setConstraintValidatorFactory(new ValidatorFactory())
            ->getValidator();

        $this->layoutResolverValidator = new LayoutResolverValidator();
        $this->layoutResolverValidator->setValidator($this->validator);
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator::validateRuleCreateStruct
     * @dataProvider validateRuleCreateStructProvider
     */
    public function testValidateRuleCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->layoutResolverValidator->validateRuleCreateStruct(new RuleCreateStruct($params))
        );
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
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->layoutResolverValidator->validateRuleUpdateStruct(new RuleUpdateStruct($params))
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
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->layoutResolverValidator->validateTargetCreateStruct(new TargetCreateStruct($params))
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
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->layoutResolverValidator->validateConditionCreateStruct(new ConditionCreateStruct($params))
        );
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
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->layoutResolverValidator->validateConditionUpdateStruct(new ConditionUpdateStruct($params))
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
            array(array('layoutId' => 12, 'priority' => 2, 'comment' => 'Comment'), true),
            array(array('layoutId' => null, 'priority' => 2, 'comment' => 'Comment'), true),
            array(array('layoutId' => '12', 'priority' => 2, 'comment' => 'Comment'), true),
            array(array('layoutId' => '', 'priority' => 2, 'comment' => 'Comment'), false),
            array(array('layoutId' => array(), 'priority' => 2, 'comment' => 'Comment'), false),
            array(array('layoutId' => 12, 'priority' => null, 'comment' => 'Comment'), true),
            array(array('layoutId' => 12, 'priority' => '2', 'comment' => 'Comment'), false),
            array(array('layoutId' => 12, 'priority' => 2, 'comment' => null), true),
            array(array('layoutId' => 12, 'priority' => 2, 'comment' => ''), true),
            array(array('layoutId' => 12, 'priority' => 2, 'comment' => 42), false),
        );
    }

    public function validateTargetCreateStructProvider()
    {
        return array(
            array(array('identifier' => 'target', 'value' => 42), true),
            array(array('identifier' => 'target', 'value' => '42'), true),
            array(array('identifier' => 'target', 'value' => array(42)), true),
            array(array('identifier' => '', 'value' => 42), false),
            array(array('identifier' => null, 'value' => 42), false),
            array(array('identifier' => 42, 'value' => 42), false),
            array(array('identifier' => 'target', 'value' => null), false),
            array(array('identifier' => 'target', 'value' => ''), false),
            array(array('identifier' => 'target', 'value' => array()), false),
        );
    }

    public function validateConditionCreateStructProvider()
    {
        return array(
            array(array('identifier' => 'target', 'value' => 42), true),
            array(array('identifier' => 'target', 'value' => '42'), true),
            array(array('identifier' => 'target', 'value' => array(42)), true),
            array(array('identifier' => '', 'value' => 42), false),
            array(array('identifier' => null, 'value' => 42), false),
            array(array('identifier' => 42, 'value' => 42), false),
            array(array('identifier' => 'target', 'value' => null), false),
            array(array('identifier' => 'target', 'value' => ''), false),
            array(array('identifier' => 'target', 'value' => array()), false),
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
