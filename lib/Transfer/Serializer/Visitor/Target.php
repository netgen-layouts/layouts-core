<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\API\Values\LayoutResolver\Target as TargetValue;
use Netgen\BlockManager\Transfer\Serializer\Visitor;

/**
 * Target value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\LayoutResolver\Target
 */
class Target extends Visitor
{
    public function accept($value)
    {
        return $value instanceof TargetValue;
    }

    public function visit($target, Visitor $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\API\Values\LayoutResolver\Target $target */

        return array(
            'id' => $target->getId(),
            'status' => $this->getStatusString($target),
            'type' => $target->getTargetType()->getType(),
            'is_published' => $target->isPublished(),
            // todo mixed what? scalar?
            'value' => $target->getValue(),
        );
    }
}
