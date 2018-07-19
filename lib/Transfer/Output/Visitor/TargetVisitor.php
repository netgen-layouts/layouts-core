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

    public function visit($target, ?VisitorInterface $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\API\Values\LayoutResolver\Target $target */

        return [
            'id' => $target->getId(),
            'type' => $target->getTargetType()->getType(),
            'value' => $target->getValue(),
        ];
    }
}
