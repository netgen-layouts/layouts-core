<?php

namespace Netgen\BlockManager\Tests\TestCase;

use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Tests\Item\Stubs\ValueLoader;
use Netgen\BlockManager\Validator\BlockItemViewTypeValidator;
use Netgen\BlockManager\Validator\BlockViewTypeValidator;
use Netgen\BlockManager\Validator\ParametersValidator;
use Netgen\BlockManager\Validator\ValueTypeValidator;
use Netgen\BlockManager\Item\Registry\ValueLoaderRegistry;
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
            $valueLoaderRegistry = new ValueLoaderRegistry();
            $valueLoaderRegistry->addValueLoader(new ValueLoader());

            return new ValueTypeValidator($valueLoaderRegistry);
        } elseif ($name === 'ngbm_parameters') {
            return new ParametersValidator(
                new ParameterFilterRegistry()
            );
        } else {
            return parent::getInstance($constraint);
        }
    }
}
