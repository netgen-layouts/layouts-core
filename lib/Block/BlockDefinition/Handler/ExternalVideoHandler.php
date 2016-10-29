<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterDefinition;

class ExternalVideoHandler extends BlockDefinitionHandler
{
    /**
     * @var array
     */
    protected $services = array();

    /**
     * Constructor.
     *
     * @param array $services
     */
    public function __construct(array $services = array())
    {
        $this->services = array_flip($services);
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters()
    {
        return array(
            'service' => new ParameterDefinition\Choice(array('options' => $this->services), true),
            'video_id' => new ParameterDefinition\TextLine(),
            'caption' => new ParameterDefinition\TextLine(),
        ) + $this->getCommonParameters();
    }
}
