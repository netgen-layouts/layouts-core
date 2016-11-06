<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

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
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'latitude',
            ParameterType\NumberType::class,
            array(
                'required' => true,
                'default_value' => 0,
                'groups' => array(self::GROUP_CONTENT),
                'min' => -90,
                'max' => 90,
                'scale' => 6,
            )
        );

        $builder->add(
            'longitude',
            ParameterType\NumberType::class,
            array(
                'required' => true,
                'default_value' => 0,
                'groups' => array(self::GROUP_CONTENT),
                'min' => -180,
                'max' => 180,
                'scale' => 6,
            )
        );

        $builder->add(
            'zoom',
            ParameterType\RangeType::class,
            array(
                'required' => true,
                'default_value' => 5,
                'groups' => array(self::GROUP_DESIGN),
                'min' => $this->minZoom,
                'max' => $this->maxZoom,
            )
        );

        $builder->add(
            'map_type',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'groups' => array(self::GROUP_DESIGN),
                'options' => $this->mapTypes,
            )
        );

        $builder->add(
            'show_marker',
            ParameterType\BooleanType::class,
            array(
                'default_value' => true,
                'groups' => array(self::GROUP_DESIGN),
            )
        );

        $this->buildCommonParameters($builder, array(self::GROUP_DESIGN));
    }
}
