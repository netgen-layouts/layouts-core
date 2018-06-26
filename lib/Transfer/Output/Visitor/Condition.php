<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition as ConditionValue;
use Netgen\BlockManager\Transfer\Output\Visitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Condition value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\LayoutResolver\Condition
 */
final class Condition extends Visitor
{
    public function accept($value): bool
    {
        return $value instanceof ConditionValue;
    }

    public function visit($condition, ?VisitorInterface $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition */

        return [
            'id' => $condition->getId(),
            'type' => $condition->getConditionType()->getType(),
            'value' => $condition->getValue(),
        ];
    }
}
