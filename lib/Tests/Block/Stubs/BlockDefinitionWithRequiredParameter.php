<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\Block\BlockDefinition as BaseBlockDefinition;
use Netgen\BlockManager\Parameters\Parameter;

class BlockDefinitionWithRequiredParameter extends BaseBlockDefinition
{
    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        $parameters = parent::getParameters();
        $parameters['css_class'] = new Parameter\Text(array(), true);

        return $parameters;
    }

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'block_definition';
    }
}
