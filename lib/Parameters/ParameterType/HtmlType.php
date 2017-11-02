<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate HTML markup.
 *
 * It will be filtered by the system to remove any unsafe markup.
 */
final class HtmlType extends ParameterType
{
    public function getIdentifier()
    {
        return 'html';
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        return array(
            new Constraints\Type(array('type' => 'string')),
        );
    }
}
