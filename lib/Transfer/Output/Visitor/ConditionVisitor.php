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

    /**
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $value
     * @param \Netgen\BlockManager\Transfer\Output\VisitorInterface|null $subVisitor
     *
     * @return mixed
     */
    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        return [
            'id' => $value->getId(),
            'type' => $value->getConditionType()::getType(),
            'value' => $value->getValue(),
        ];
    }
}
