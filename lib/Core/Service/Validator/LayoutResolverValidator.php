<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Symfony\Component\Validator\Constraints;

class LayoutResolverValidator extends Validator
{
    /**
     * Validates rule create struct.
     *
     * @param \Netgen\BlockManager\API\Values\RuleCreateStruct $ruleCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     */
    public function validateRuleCreateStruct(RuleCreateStruct $ruleCreateStruct)
    {
        $this->validate(
            $ruleCreateStruct->layoutId,
            array(
                new Constraints\Type(array('type' => 'int')),
            ),
            'layoutId'
        );

        $this->validate(
            $ruleCreateStruct->priority,
            array(
                new Constraints\Type(array('type' => 'int')),
            ),
            'priority'
        );

        $this->validate(
            $ruleCreateStruct->enabled,
            array(
                new Constraints\Type(array('type' => 'bool')),
            ),
            'enabled'
        );

        $this->validate(
            $ruleCreateStruct->comment,
            array(
                new Constraints\Type(array('type' => 'string')),
            ),
            'comment'
        );

        $this->validate(
            $ruleCreateStruct->status,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'int')),
            ),
            'status'
        );
    }

    /**
     * Validates rule update struct.
     *
     * @param \Netgen\BlockManager\API\Values\RuleUpdateStruct $ruleUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     */
    public function validateRuleUpdateStruct(RuleUpdateStruct $ruleUpdateStruct)
    {
        $this->validate(
            $ruleUpdateStruct->layoutId,
            array(
                new Constraints\Type(array('type' => 'int')),
            ),
            'layoutId'
        );

        $this->validate(
            $ruleUpdateStruct->priority,
            array(
                new Constraints\Type(array('type' => 'int')),
            ),
            'priority'
        );

        $this->validate(
            $ruleUpdateStruct->comment,
            array(
                new Constraints\Type(array('type' => 'string')),
            ),
            'comment'
        );
    }

    /**
     * Validates target create struct.
     *
     * @param \Netgen\BlockManager\API\Values\TargetCreateStruct $targetCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     */
    public function validateTargetCreateStruct(TargetCreateStruct $targetCreateStruct)
    {
        $this->validate(
            $targetCreateStruct->identifier,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'identifier'
        );

        $this->validate(
            $targetCreateStruct->value,
            array(
                new Constraints\NotBlank(),
            ),
            'value'
        );
    }

    /**
     * Validates condition create struct.
     *
     * @param \Netgen\BlockManager\API\Values\ConditionCreateStruct $conditionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     */
    public function validateConditionCreateStruct(ConditionCreateStruct $conditionCreateStruct)
    {
        $this->validate(
            $conditionCreateStruct->identifier,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'identifier'
        );

        $this->validate(
            $conditionCreateStruct->value,
            array(
                new Constraints\NotBlank(),
            ),
            'value'
        );
    }

    /**
     * Validates condition update struct.
     *
     * @param \Netgen\BlockManager\API\Values\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     */
    public function validateConditionUpdateStruct(ConditionUpdateStruct $conditionUpdateStruct)
    {
        $this->validate(
            $conditionUpdateStruct->value,
            array(
                new Constraints\NotBlank(),
            ),
            'value'
        );
    }
}
