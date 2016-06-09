<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

class MapHandler extends BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
    /**
     * @var int
     */
    protected $minZoom;

    /**
     * @var int
     */
    protected $maxZoom;

    /**
     * @var array
     */
    protected $mapTypes = array();

    public function __construct($minZoom = null, $maxZoom = null, array $mapTypes = array())
    {
        $this->minZoom = $minZoom;
        $this->maxZoom = $maxZoom;
        $this->mapTypes = array_flip($mapTypes);
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'latitude' => new Parameter\Number(array(), true),
            'longitude' => new Parameter\Number(array(), true),
            'zoom' => new Parameter\Integer(array(), true),
            'map_type' => new Parameter\Select(array('options' => $this->mapTypes), true),
            'show_marker' => new Parameter\Boolean(array(), true),
        ) + parent::getParameters();
    }
}
