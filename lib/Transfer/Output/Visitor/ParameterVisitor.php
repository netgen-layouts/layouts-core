<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Parameter value visitor.
 *
 * @see \Netgen\Layouts\Parameters\Parameter
 */
final class ParameterVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Parameter;
    }

    /**
     * @param \Netgen\Layouts\Parameters\Parameter $value
     * @param \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor $aggregateVisitor
     *
     * @return mixed
     */
    public function visit($value, AggregateVisitor $aggregateVisitor)
    {
        $valueDefinition = $value->getParameterDefinition();

        return $valueDefinition->getType()->export($valueDefinition, $value->getValue());
    }
}
