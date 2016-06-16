<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

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
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'service' => new Parameter\Choice(array('options' => $this->services), true),
            'video_id' => new Parameter\TextLine(),
            'caption' => new Parameter\TextLine(),
        ) + parent::getParameters();
    }
}
