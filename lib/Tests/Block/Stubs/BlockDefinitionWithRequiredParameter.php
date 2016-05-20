<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\Block\BlockDefinition as BaseBlockDefinition;
use Netgen\BlockManager\Parameters\Parameter;

class BlockDefinitionWithRequiredParameter extends BaseBlockDefinition
{
    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'block_definition';
    }

    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'css_class' => new Parameter\Text(array(), true),
            'css_id' => new Parameter\Text(array()),
        );
    }
}
