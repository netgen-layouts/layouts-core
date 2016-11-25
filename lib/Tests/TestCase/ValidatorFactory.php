<?php

namespace Netgen\BlockManager\Tests\TestCase;

use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class ValidatorFactory extends ConstraintValidatorFactory
{
    /**
     * @var \PHPUnit\Framework\TestCase
     */
    protected $testCase;

    /**
     * Constructor.
     *
     * @param \PHPUnit\Framework\TestCase $testCase
     */
    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if ($name === 'ngbm_block_view_type') {
            return new Validator\BlockViewTypeValidator();
        } elseif ($name === 'ngbm_block_item_view_type') {
            return new Validator\BlockItemViewTypeValidator();
        } elseif ($name === 'ngbm_value_type') {
            return new Validator\ValueTypeValidator(array('value'));
        } elseif ($name === 'ngbm_link') {
            return new Validator\Parameters\LinkValidator();
        } elseif ($name === 'ngbm_item_link') {
            $itemLoader = $this->testCase
                ->getMockBuilder(ItemLoaderInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

            return new Validator\Parameters\ItemLinkValidator($itemLoader);
        } elseif ($name === 'ngbm_parameter_struct') {
            return new Validator\Structs\ParameterStructValidator(new ParameterFilterRegistry());
        } elseif ($name === 'ngbm_block_update_struct') {
            return new Validator\Structs\BlockUpdateStructValidator();
        } elseif ($name === 'ngbm_query_update_struct') {
            return new Validator\Structs\QueryUpdateStructValidator();
        }

        return parent::getInstance($constraint);
    }
}
