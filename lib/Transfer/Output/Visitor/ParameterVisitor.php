<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Parameter value visitor.
 *
 * @see \Netgen\BlockManager\Parameters\Parameter
 */
final class ParameterVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Parameter;
    }

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        /** @var \Netgen\BlockManager\Parameters\Parameter $value */
        $valueDefinition = $value->getParameterDefinition();

        return $valueDefinition->getType()->export($valueDefinition, $value->getValue());
    }
}
