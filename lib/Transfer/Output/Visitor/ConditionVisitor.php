<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Condition value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\LayoutResolver\Condition
 */
final class ConditionVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Condition;
    }

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        /** @var \Netgen\BlockManager\API\Values\LayoutResolver\Condition $value */

        return [
            'id' => $value->getId(),
            'type' => $value->getConditionType()::getType(),
            'value' => $value->getValue(),
        ];
    }
}
