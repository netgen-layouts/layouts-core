<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

class MapHandler extends BlockDefinitionHandler
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

    public function __construct($minZoom, $maxZoom, array $mapTypes = array())
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
            'latitude' => new Parameter\Number(
                array(
                    'min' => -90,
                    'max' => 90,
                ),
                true
            ),
            'longitude' => new Parameter\Number(
                array(
                    'min' => -180,
                    'max' => 180,
                ),
                true
            ),
            'zoom' => new Parameter\Range(
                array(
                    'min' => $this->minZoom,
                    'max' => $this->maxZoom,
                ),
                true
            ),
            'map_type' => new Parameter\Choice(array('options' => $this->mapTypes), true),
            'show_marker' => new Parameter\Boolean(),
        ) + parent::getParameters();
    }
}
