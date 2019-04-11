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

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        /** @var \Netgen\Layouts\Parameters\Parameter $value */
        $valueDefinition = $value->getParameterDefinition();

        return $valueDefinition->getType()->export($valueDefinition, $value->getValue());
    }
}
