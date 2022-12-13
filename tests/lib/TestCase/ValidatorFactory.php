<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use Netgen\Layouts\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class ValidatorFactory implements ConstraintValidatorFactoryInterface
{
    private TestCase $testCase;

    private ConstraintValidatorFactory $baseValidatorFactory;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
        $this->baseValidatorFactory = new ConstraintValidatorFactory();
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $name = $constraint->validatedBy();

        if ($name === 'nglayouts_block_view_type') {
            return new Validator\BlockViewTypeValidator();
        }

        if ($name === 'nglayouts_block_item_view_type') {
            return new Validator\BlockItemViewTypeValidator();
        }

        if ($name === 'nglayouts_value_type') {
            $valueTypeRegistry = new ValueTypeRegistry(
                [
                    'value' => ValueType::fromArray(['isEnabled' => true]),
                    'default' => ValueType::fromArray(['isEnabled' => true]),
                ],
            );

            return new Validator\ValueTypeValidator($valueTypeRegistry);
        }

        if ($name === 'nglayouts_datetime') {
            return new Validator\DateTimeValidator();
        }

        if ($name === 'nglayouts_locale') {
            return new Validator\LocaleValidator();
        }

        if ($name === 'nglayouts_link') {
            return new Validator\Parameters\LinkValidator();
        }

        if ($name === 'nglayouts_item_link') {
            $cmsItemLoader = $this->testCase
                ->getMockBuilder(CmsItemLoaderInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

            return new Validator\Parameters\ItemLinkValidator($cmsItemLoader);
        }

        if ($name === 'nglayouts_condition_type_time') {
            return new Validator\ConditionType\TimeValidator();
        }

        if ($name === 'nglayouts_parameter_struct') {
            return new Validator\Structs\ParameterStructValidator();
        }

        if ($name === 'nglayouts_block_create_struct') {
            return new Validator\Structs\BlockCreateStructValidator();
        }

        if ($name === 'nglayouts_block_update_struct') {
            return new Validator\Structs\BlockUpdateStructValidator();
        }

        if ($name === 'nglayouts_query_update_struct') {
            return new Validator\Structs\QueryUpdateStructValidator();
        }

        if ($name === 'nglayouts_config_aware_struct') {
            return new Validator\Structs\ConfigAwareStructValidator();
        }

        if ($name === EmailValidator::class && Kernel::VERSION_ID >= 60200 && Kernel::VERSION_ID < 70000) {
            // Default email validator option `loose` is deprecated since 6.2
            return new EmailValidator(Email::VALIDATION_MODE_STRICT);
        }

        return $this->baseValidatorFactory->getInstance($constraint);
    }
}
