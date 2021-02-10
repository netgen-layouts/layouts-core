<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Validator;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints;
use function is_bool;
use function sprintf;

final class LayoutResolverValidator
{
    use ValidatorTrait;

    private TargetTypeRegistry $targetTypeRegistry;

    private ConditionTypeRegistry $conditionTypeRegistry;

    public function __construct(
        TargetTypeRegistry $targetTypeRegistry,
        ConditionTypeRegistry $conditionTypeRegistry
    ) {
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Validates the provided rule update struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateRuleUpdateStruct(RuleUpdateStruct $ruleUpdateStruct): void
    {
        if (is_bool($ruleUpdateStruct->layoutId)) {
            $this->validate(
                $ruleUpdateStruct->layoutId,
                [
                    new Constraints\IdenticalTo(['value' => false]),
                ],
                'layoutId'
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
        if (!isset($targetCreateStruct->type)) {
            throw ValidationException::validationFailed('type', sprintf('"type" is required in %s', TargetCreateStruct::class));
        }

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
        if (!isset($conditionCreateStruct->type)) {
            throw ValidationException::validationFailed('type', sprintf('"type" is required in %s', ConditionCreateStruct::class));
        }

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
