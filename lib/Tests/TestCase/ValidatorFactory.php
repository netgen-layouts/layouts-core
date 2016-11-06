<?php

namespace Netgen\BlockManager\Tests\TestCase;

use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Validator\BlockItemViewTypeValidator;
use Netgen\BlockManager\Validator\BlockViewTypeValidator;
use Netgen\BlockManager\Validator\Structs\ParameterStructValidator;
use Netgen\BlockManager\Validator\Structs\BlockUpdateStructValidator;
use Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator;
use Netgen\BlockManager\Validator\ValueTypeValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class ValidatorFactory extends ConstraintValidatorFactory
{
    /**
     * {@inheritdoc}
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if ($name === 'ngbm_block_view_type') {
            return new BlockViewTypeValidator();
        } elseif ($name === 'ngbm_block_item_view_type') {
            return new BlockItemViewTypeValidator();
        } elseif ($name === 'ngbm_value_type') {
            return new ValueTypeValidator(array('value'));
        } elseif ($name === 'ngbm_parameter_struct') {
            return new ParameterStructValidator(new ParameterFilterRegistry());
        } elseif ($name === 'ngbm_block_update_struct') {
            return new BlockUpdateStructValidator();
        } elseif ($name === 'ngbm_query_update_struct') {
            return new QueryUpdateStructValidator();
        }

        return parent::getInstance($constraint);
    }
}
