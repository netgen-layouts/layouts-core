<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Collection slot value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Collection\Slot
 */
final class SlotVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Slot;
    }

    /**
     * @param \Netgen\Layouts\API\Values\Collection\Slot $value
     * @param \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor $aggregateVisitor
     *
     * @return mixed
     */
    public function visit($value, AggregateVisitor $aggregateVisitor)
    {
        return [
            'id' => $value->getId()->toString(),
            'position' => $value->getPosition(),
            'view_type' => $value->getViewType(),
        ];
    }
}
