<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Condition value visitor.
 *
 * @see \Netgen\Layouts\API\Values\LayoutResolver\Condition
 */
final class ConditionVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Condition;
    }

    /**
     * @param \Netgen\Layouts\API\Values\LayoutResolver\Condition $value
     * @param \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor $aggregateVisitor
     *
     * @return mixed
     */
    public function visit($value, AggregateVisitor $aggregateVisitor)
    {
        return [
            'id' => $value->getId()->toString(),
            'type' => $value->getConditionType()::getType(),
            'value' => $value->getValue(),
        ];
    }
}
