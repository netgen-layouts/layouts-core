<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Target value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\LayoutResolver\Target
 */
final class TargetVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Target;
    }

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\API\Values\LayoutResolver\Target $value */

        return [
            'id' => $value->getId(),
            'type' => $value->getTargetType()::getType(),
            'value' => $value->getValue(),
        ];
    }
}
