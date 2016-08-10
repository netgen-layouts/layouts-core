<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\TargetUpdateStruct;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Symfony\Component\Validator\Constraints;

class LayoutResolverValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    protected $conditionTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface $targetTypeRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface $conditionTypeRegistry
     */
    public function __construct(
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry
    ) {
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Validates rule create struct.
     *
     * @param \Netgen\BlockManager\API\Values\RuleCreateStruct $ruleCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateRuleCreateStruct(RuleCreateStruct $ruleCreateStruct)
    {
        if ($ruleCreateStruct->layoutId !== null) {
            $this->validate(
                $ruleCreateStruct->layoutId,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'scalar')),
                ),
                'layoutId'
            );
        }

        if ($ruleCreateStruct->priority !== null) {
            $this->validate(
                $ruleCreateStruct->priority,
                array(
                    new Constraints\Type(array('type' => 'int')),
                ),
                'priority'
            );
        }

        if ($ruleCreateStruct->enabled !== null) {
            $this->validate(
                $ruleCreateStruct->enabled,
                array(
                    new Constraints\Type(array('type' => 'bool')),
                ),
                'enabled'
            );
        }

        if ($ruleCreateStruct->comment !== null) {
            $this->validate(
                $ruleCreateStruct->comment,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'comment'
            );
        }

        return true;
    }

    /**
     * Validates rule update struct.
     *
     * @param \Netgen\BlockManager\API\Values\RuleUpdateStruct $ruleUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateRuleUpdateStruct(RuleUpdateStruct $ruleUpdateStruct)
    {
        if ($ruleUpdateStruct->layoutId !== null) {
            $this->validate(
                $ruleUpdateStruct->layoutId,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'scalar')),
                ),
                'layoutId'
            );
        }

        if ($ruleUpdateStruct->comment !== null) {
            $this->validate(
                $ruleUpdateStruct->comment,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'comment'
            );
        }

        return true;
    }

    /**
     * Validates rule metadata update struct.
     *
     * @param \Netgen\BlockManager\API\Values\RuleMetadataUpdateStruct $ruleUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateRuleMetadataUpdateStruct(RuleMetadataUpdateStruct $ruleUpdateStruct)
    {
        if ($ruleUpdateStruct->priority !== null) {
            $this->validate(
                $ruleUpdateStruct->priority,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'int')),
                ),
                'priority'
            );
        }

        return true;
    }

    /**
     * Validates target create struct.
     *
     * @param \Netgen\BlockManager\API\Values\TargetCreateStruct $targetCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateTargetCreateStruct(TargetCreateStruct $targetCreateStruct)
    {
        $this->validate(
            $targetCreateStruct->type,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'type'
        );

        $targetType = $this->targetTypeRegistry->getTargetType($targetCreateStruct->type);

        $this->validate(
            $targetCreateStruct->value,
            $targetType->getConstraints(),
            'value'
        );

        return true;
    }

    /**
     * Validates target update struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     * @param \Netgen\BlockManager\API\Values\TargetUpdateStruct $targetUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool If the validation failed
     */
    public function validateTargetUpdateStruct(Target $target, TargetUpdateStruct $targetUpdateStruct)
    {
        $targetType = $target->getTargetType();

        $this->validate(
            $targetUpdateStruct->value,
            $targetType->getConstraints(),
            'value'
        );

        return true;
    }

    /**
     * Validates condition create struct.
     *
     * @param \Netgen\BlockManager\API\Values\ConditionCreateStruct $conditionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateConditionCreateStruct(ConditionCreateStruct $conditionCreateStruct)
    {
        $this->validate(
            $conditionCreateStruct->type,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'type'
        );

        $conditionType = $this->conditionTypeRegistry->getConditionType($conditionCreateStruct->type);

        $this->validate(
            $conditionCreateStruct->value,
            $conditionType->getConstraints(),
            'value'
        );

        return true;
    }

    /**
     * Validates condition update struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     * @param \Netgen\BlockManager\API\Values\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool If the validation failed
     */
    public function validateConditionUpdateStruct(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct)
    {
        $conditionType = $condition->getConditionType();

        $this->validate(
            $conditionUpdateStruct->value,
            $conditionType->getConstraints(),
            'value'
        );

        return true;
    }
}
