<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\Parameters\ParameterValue as ParameterValueValue;
use Netgen\BlockManager\Transfer\Output\Visitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * ParameterValue value visitor.
 *
 * @see \Netgen\BlockManager\Parameters\ParameterValue
 */
final class ParameterValue extends Visitor
{
    public function accept($value)
    {
        return $value instanceof ParameterValueValue;
    }

    public function visit($parameterValue, VisitorInterface $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\Parameters\ParameterValue $parameterValue */

        $parameter = $parameterValue->getParameter();

        return $parameter->getType()->export($parameter, $parameterValue->getValue());
    }
}
