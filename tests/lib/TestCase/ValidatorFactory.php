<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use Netgen\Layouts\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class ValidatorFactory implements ConstraintValidatorFactoryInterface
{
    private ConstraintValidatorFactory $baseValidatorFactory;

    public function __construct(
        private LayoutService $layoutService,
        private LayoutResolverService $layoutResolverService,
        private CmsItemLoaderInterface $cmsItemLoader,
    ) {
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

        if ($name === 'nglayouts_block_definition') {
            $blockDefinitionRegistry = new BlockDefinitionRegistry(
                [
                    'title' => BlockDefinition::fromArray(['identifier' => 'title']),
                    'text' => BlockDefinition::fromArray(['identifier' => 'text']),
                ],
            );

            return new Validator\BlockDefinitionValidator($blockDefinitionRegistry);
        }

        if ($name === 'nglayouts_layout') {
            return new Validator\LayoutValidator($this->layoutService);
        }

        if ($name === 'nglayouts_rule_group') {
            return new Validator\RuleGroupValidator($this->layoutResolverService);
        }

        if ($name === 'nglayouts_datetime') {
            return new Validator\DateTimeValidator();
        }

        if ($name === 'nglayouts_link') {
            return new Validator\Parameters\LinkValidator();
        }

        if ($name === 'nglayouts_item_link') {
            return new Validator\Parameters\ItemLinkValidator($this->cmsItemLoader);
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

        return $this->baseValidatorFactory->getInstance($constraint);
    }
}
