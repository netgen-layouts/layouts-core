<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition as ConditionValue;
use Netgen\BlockManager\Transfer\Output\Visitor;

/**
 * Condition value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\LayoutResolver\Condition
 */
final class Condition extends Visitor
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
            'value' => $condition->getValue(),
        );
    }
}
