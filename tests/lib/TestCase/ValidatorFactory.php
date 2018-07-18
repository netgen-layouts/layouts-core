<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\TestCase;

use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

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

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
        $this->baseValidatorFactory = new ConstraintValidatorFactory();
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $name = $constraint->validatedBy();

        if ($name === 'ngbm_block_view_type') {
            return new Validator\BlockViewTypeValidator();
        }

        if ($name === 'ngbm_block_item_view_type') {
            return new Validator\BlockItemViewTypeValidator();
        }

        if ($name === 'ngbm_value_type') {
            $valueTypeRegistry = new ValueTypeRegistry(
                [
                    'value' => ValueType::fromArray(['isEnabled' => true]),
                    'default' => ValueType::fromArray(['isEnabled' => true]),
                ]
            );

            return new Validator\ValueTypeValidator($valueTypeRegistry);
        }

        if ($name === 'ngbm_datetime') {
            return new Validator\DateTimeValidator();
        }

        if ($name === 'ngbm_locale') {
            return new Validator\LocaleValidator();
        }

        if ($name === 'ngbm_link') {
            return new Validator\Parameters\LinkValidator();
        }

        if ($name === 'ngbm_item_link') {
            $cmsItemLoader = $this->testCase
                ->getMockBuilder(CmsItemLoaderInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

            return new Validator\Parameters\ItemLinkValidator($cmsItemLoader);
        }

        if ($name === 'ngbm_condition_type_time') {
            return new Validator\ConditionType\TimeValidator();
        }

        if ($name === 'ngbm_parameter_struct') {
            return new Validator\Structs\ParameterStructValidator();
        }

        if ($name === 'ngbm_block_create_struct') {
            return new Validator\Structs\BlockCreateStructValidator();
        }

        if ($name === 'ngbm_block_update_struct') {
            return new Validator\Structs\BlockUpdateStructValidator();
        }

        if ($name === 'ngbm_query_update_struct') {
            return new Validator\Structs\QueryUpdateStructValidator();
        }

        if ($name === 'ngbm_config_aware_struct') {
            return new Validator\Structs\ConfigAwareStructValidator();
        }

        return $this->baseValidatorFactory->getInstance($constraint);
    }
}
