<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\Parameters\Parameter as ParameterValue;
use Netgen\BlockManager\Transfer\Output\Visitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Parameter value visitor.
 *
 * @see \Netgen\BlockManager\Parameters\Parameter
 */
final class Parameter extends Visitor
{
    public function accept($value)
    {
        return $value instanceof ParameterValue;
    }

    public function visit($parameter, VisitorInterface $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\Parameters\Parameter $parameter */

        $parameterDefinition = $parameter->getParameterDefinition();

        return $parameterDefinition->getType()->export($parameterDefinition, $parameter->getValue());
    }
}
