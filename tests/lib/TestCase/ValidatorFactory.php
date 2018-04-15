<?php

namespace Netgen\BlockManager\Tests\TestCase;

use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

final class ValidatorFactory implements ConstraintValidatorFactoryInterface
{
    /**
     * @var \PHPUnit\Framework\TestCase
     */
    private $testCase;

    /**
     * @var \Symfony\Component\Validator\ConstraintValidatorFactoryInterface
     */
    private $baseValidatorFactory;

    /**
     * @param \PHPUnit\Framework\TestCase $testCase
     */
    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
        $this->baseValidatorFactory = new ConstraintValidatorFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if ($name === 'ngbm_block_view_type') {
            return new Validator\BlockViewTypeValidator();
        }

        if ($name === 'ngbm_block_item_view_type') {
            return new Validator\BlockItemViewTypeValidator();
        }

        if ($name === 'ngbm_value_type') {
            $valueTypeRegistry = new ValueTypeRegistry();
            $valueTypeRegistry->addValueType('value', new ValueType(array('isEnabled' => true)));
            $valueTypeRegistry->addValueType('default', new ValueType(array('isEnabled' => true)));

            return new Validator\ValueTypeValidator($valueTypeRegistry);
        }

        if ($name === 'ngbm_datetime') {
            return new Validator\DateTimeValidator();
        }

        if ($name === 'ngbm_link') {
            return new Validator\Parameters\LinkValidator();
        }

        if ($name === 'ngbm_item_link') {
            $itemLoader = $this->testCase
                ->getMockBuilder(ItemLoaderInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

            return new Validator\Parameters\ItemLinkValidator($itemLoader);
        }

        if ($name === 'ngbm_parameter_struct') {
            return new Validator\Structs\ParameterStructValidator(new ParameterFilterRegistry());
        }

        if ($name === 'ngbm_block_update_struct') {
            return new Validator\Structs\BlockUpdateStructValidator();
        }

        if ($name === 'ngbm_query_update_struct') {
            return new Validator\Structs\QueryUpdateStructValidator();
        }

        return $this->baseValidatorFactory->getInstance($constraint);
    }
}
