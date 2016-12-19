<?php

namespace Netgen\BlockManager\Tests\Layout\Container\Stubs;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\DynamicContainerDefinitionHandler as BaseDynamicContainerDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;

class DynamicContainerDefinitionHandler extends BaseDynamicContainerDefinitionHandler
{
    /**
     * Returns the array specifying container parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'css_class' => new Parameter('css_class', new ParameterType\TextLineType()),
            'css_id' => new Parameter('css_id', new ParameterType\TextLineType()),
        );
    }

    /**
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers()
    {
        return array();
    }
}
