<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Symfony\Component\Validator\Constraints;

final class LayoutResolverValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    private $conditionTypeRegistry;

    public function __construct(
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry
    ) {
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Validates the provided rule create struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct $ruleCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateRuleCreateStruct(RuleCreateStruct $ruleCreateStruct)
    {
        if ($ruleCreateStruct->layoutId !== null) {
            $this->validate(
                $ruleCreateStruct->layoutId,
                [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'scalar']),
                ],
                'layoutId'
            );
        }

        if ($ruleCreateStruct->priority !== null) {
            $this->validate(
                $ruleCreateStruct->priority,
                [
                    new Constraints\Type(['type' => 'int']),
                ],
                'priority'
            );
        }

        if ($ruleCreateStruct->enabled !== null) {
            $this->validate(
                $ruleCreateStruct->enabled,
                [
                    new Constraints\Type(['type' => 'bool']),
                ],
                'enabled'
            );
        }

        if ($ruleCreateStruct->comment !== null) {
            $this->validate(
                $ruleCreateStruct->comment,
                [
                    new Constraints\Type(['type' => 'string']),
                ],
                'comment'
            );
        }
    }

    /**
     * Validates the provided rule update struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct $ruleUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateRuleUpdateStruct(RuleUpdateStruct $ruleUpdateStruct)
    {
        if ($ruleUpdateStruct->layoutId !== null) {
            $this->validate(
                $ruleUpdateStruct->layoutId,
                [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'scalar']),
                ],
                'layoutId'
            );
        }

        if ($ruleUpdateStruct->comment !== null) {
            $this->validate(
                $ruleUpdateStruct->comment,
                [
                    new Constraints\Type(['type' => 'string']),
                ],
                'comment'
            );
        }
    }

    /**
     * Validates the provided rule metadata update struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct $ruleUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateRuleMetadataUpdateStruct(RuleMetadataUpdateStruct $ruleUpdateStruct)
    {
        if ($ruleUpdateStruct->priority !== null) {
            $this->validate(
                $ruleUpdateStruct->priority,
                [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'int']),
                ],
                'priority'
            );
        }
    }

    /**
     * Validates the provided target create struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct $targetCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateTargetCreateStruct(TargetCreateStruct $targetCreateStruct)
    {
        $this->validate(
            $targetCreateStruct->type,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'type'
        );

        $targetType = $this->targetTypeRegistry->getTargetType($targetCreateStruct->type);

        $this->validate(
            $targetCreateStruct->value,
            $targetType->getConstraints(),
            'value'
        );
    }

    /**
     * Validates the provided target update struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct $targetUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateTargetUpdateStruct(Target $target, TargetUpdateStruct $targetUpdateStruct)
    {
        $targetType = $target->getTargetType();

        $this->validate(
            $targetUpdateStruct->value,
            $targetType->getConstraints(),
            'value'
        );
    }

    /**
     * Validates the provided condition create struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct $conditionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateConditionCreateStruct(ConditionCreateStruct $conditionCreateStruct)
    {
        $this->validate(
            $conditionCreateStruct->type,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'type'
        );

        $conditionType = $this->conditionTypeRegistry->getConditionType($conditionCreateStruct->type);

        $this->validate(
            $conditionCreateStruct->value,
            $conditionType->getConstraints(),
            'value'
        );
    }

    /**
     * Validates the provided condition update struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateConditionUpdateStruct(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct)
    {
        $conditionType = $condition->getConditionType();

        $this->validate(
            $conditionUpdateStruct->value,
            $conditionType->getConstraints(),
            'value'
        );
    }
}
