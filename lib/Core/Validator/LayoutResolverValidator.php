<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Validator;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Validator\ValidatorTrait;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints;

final class LayoutResolverValidator
{
    use ValidatorTrait;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry
     */
    private $targetTypeRegistry;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry
     */
    private $conditionTypeRegistry;

    public function __construct(
        TargetTypeRegistry $targetTypeRegistry,
        ConditionTypeRegistry $conditionTypeRegistry
    ) {
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Validates the provided rule create struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateRuleCreateStruct(RuleCreateStruct $ruleCreateStruct): void
    {
        if ($ruleCreateStruct->uuid !== null) {
            $this->validate(
                $ruleCreateStruct->uuid,
                [
                    new Constraints\Type(['type' => UuidInterface::class]),
                ],
                'uuid'
            );
        }

        if ($ruleCreateStruct->layoutId !== null) {
            $this->validate(
                $ruleCreateStruct->layoutId,
                [
                    new Constraints\Type(['type' => UuidInterface::class]),
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

        if (isset($ruleCreateStruct->enabled)) {
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
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateRuleUpdateStruct(RuleUpdateStruct $ruleUpdateStruct): void
    {
        if ($ruleUpdateStruct->layoutId !== null && $ruleUpdateStruct->layoutId !== false) {
            $this->validate(
                $ruleUpdateStruct->layoutId,
                [
                    new Constraints\Type(['type' => UuidInterface::class]),
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
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateRuleMetadataUpdateStruct(RuleMetadataUpdateStruct $ruleUpdateStruct): void
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
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateTargetCreateStruct(TargetCreateStruct $targetCreateStruct): void
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
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateTargetUpdateStruct(Target $target, TargetUpdateStruct $targetUpdateStruct): void
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
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateConditionCreateStruct(ConditionCreateStruct $conditionCreateStruct): void
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
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateConditionUpdateStruct(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct): void
    {
        $conditionType = $condition->getConditionType();

        $this->validate(
            $conditionUpdateStruct->value,
            $conditionType->getConstraints(),
            'value'
        );
    }
}
