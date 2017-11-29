<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\LayoutResolver\Target as TargetValue;
use Netgen\BlockManager\Transfer\Output\Visitor;

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

    public function visit($target, Visitor $subVisitor = null, array $context = null)
    {
        /* @var \Netgen\BlockManager\API\Values\LayoutResolver\Target $target */

        return array(
            'id' => $target->getId(),
            'type' => $target->getTargetType()->getType(),
            'value' => $target->getValue(),
        );
    }
}
