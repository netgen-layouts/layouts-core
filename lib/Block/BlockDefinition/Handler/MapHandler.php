<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterDefinition;

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

    /**
     * Constructor.
     *
     * @param int $minZoom
     * @param int $maxZoom
     * @param array $mapTypes
     */
    public function __construct($minZoom, $maxZoom, array $mapTypes = array())
    {
        $this->minZoom = $minZoom;
        $this->maxZoom = $maxZoom;
        $this->mapTypes = array_flip($mapTypes);
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters()
    {
        return array(
            'latitude' => new ParameterDefinition\Number(
                array(
                    'min' => -90,
                    'max' => 90,
                    'scale' => 6,
                ),
                true,
                0,
                array(self::GROUP_CONTENT)
            ),
            'longitude' => new ParameterDefinition\Number(
                array(
                    'min' => -180,
                    'max' => 180,
                    'scale' => 6,
                ),
                true,
                0,
                array(self::GROUP_CONTENT)
            ),
            'zoom' => new ParameterDefinition\Range(
                array(
                    'min' => $this->minZoom,
                    'max' => $this->maxZoom,
                ),
                true,
                null,
                array(self::GROUP_DESIGN)
            ),
            'map_type' => new ParameterDefinition\Choice(
                array(
                    'options' => $this->mapTypes,
                ),
                true,
                null,
                array(self::GROUP_DESIGN)
            ),
            'show_marker' => new ParameterDefinition\Boolean(
                array(),
                false,
                null,
                array(self::GROUP_DESIGN)
            ),
        ) + $this->getCommonParameters(array(self::GROUP_DESIGN));
    }
}
