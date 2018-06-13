<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\LayoutResolver\Target as TargetValue;
use Netgen\BlockManager\Transfer\Output\Visitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Target value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\LayoutResolver\Target
 */
final class Target extends Visitor
{
    public function accept($value)
    {
        return $value instanceof TargetValue;
    }

    public function visit($target, VisitorInterface $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\API\Values\LayoutResolver\Target $target */

        return [
            'id' => $target->getId(),
            'type' => $target->getTargetType()->getType(),
            'value' => $target->getValue(),
        ];
    }
}
