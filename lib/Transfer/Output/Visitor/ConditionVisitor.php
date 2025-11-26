<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Condition value visitor.
 *
 * @see \Netgen\Layouts\API\Values\LayoutResolver\Condition
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\LayoutResolver\Condition>
 */
final class ConditionVisitor implements VisitorInterface
{
    public function accept(object $value): bool
    {
        return $value instanceof Condition;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'id' => $value->id->toString(),
            'type' => $value->conditionType::getType(),
            'value' => $value->conditionType->export($value->value),
        ];
    }
}
