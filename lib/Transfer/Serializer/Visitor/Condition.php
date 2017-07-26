<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition as ConditionValue;
use Netgen\BlockManager\Transfer\Serializer\Visitor;

/**
 * Condition value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\LayoutResolver\Condition
 */
class Condition extends Visitor
{
    public function accept($value)
    {
        return $value instanceof ConditionValue;
    }

    public function visit($condition, Visitor $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition */

        return array(
            'id' => $condition->getId(),
            'type' => $condition->getConditionType()->getType(),
            'status' => $this->getStatusString($condition),
            'is_published' => $condition->isPublished(),
            'constraints' => $this->visitConstraints($condition),
            'value' => $condition->getValue(),
        );
    }

    /**
     * Visit the given $condition constraints into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     *
     * @return mixed
     */
    private function visitConstraints(ConditionValue $condition)
    {
        $hash = array();
        $constraints = $condition->getConditionType()->getConstraints();

        foreach ($constraints as $constraint) {
            $hash[] = get_class($constraint);
        }

        return $hash;
    }
}
