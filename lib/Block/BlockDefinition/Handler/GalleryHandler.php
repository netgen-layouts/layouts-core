<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterDefinition;

class GalleryHandler extends BlockDefinitionHandler
{
    /**
     * @var int
     */
    protected $minAutoplayTime;

    /**
     * @var int
     */
    protected $maxAutoplayTime;

    /**
     * @var array
     */
    protected $paginationTypes = array();

    /**
     * @var array
     */
    protected $transitions = array();

    /**
     * @var array
     */
    protected $aspectRatios = array();

    /**
     * Constructor.
     *
     * @param int $minAutoplayTime
     * @param int $maxAutoplayTime
     * @param array $paginationTypes
     * @param array $transitions
     * @param array $aspectRatios
     */
    public function __construct(
        $minAutoplayTime,
        $maxAutoplayTime,
        array $paginationTypes = array(),
        array $transitions = array(),
        array $aspectRatios = array()
    ) {
        $this->minAutoplayTime = $minAutoplayTime;
        $this->maxAutoplayTime = $maxAutoplayTime;
        $this->paginationTypes = array_flip($paginationTypes);
        $this->transitions = array_flip($transitions);
        $this->aspectRatios = array_flip($aspectRatios);
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters()
    {
        return array(
            'next_and_previous' => new ParameterDefinition\Boolean(
                array(),
                false,
                null,
                array(self::GROUP_DESIGN)
            ),
            'show_pagination' => new ParameterDefinition\Compound\Boolean(
                array(
                    'pagination_type' => new ParameterDefinition\Choice(
                        array(
                            'options' => $this->paginationTypes,
                        ),
                        true,
                        null,
                        array(self::GROUP_DESIGN)
                    ),
                ),
                array(),
                false,
                null,
                array(self::GROUP_DESIGN)
            ),
            'infinite_loop' => new ParameterDefinition\Boolean(
                array(),
                false,
                null,
                array(self::GROUP_DESIGN)
            ),
            'transition' => new ParameterDefinition\Choice(
                array(
                    'options' => $this->transitions,
                ),
                true,
                null,
                array(self::GROUP_DESIGN)
            ),
            'autoplay' => new ParameterDefinition\Compound\Boolean(
                array(
                    'autoplay_time' => new ParameterDefinition\Range(
                        array(
                            'min' => $this->minAutoplayTime,
                            'max' => $this->maxAutoplayTime,
                        ),
                        true,
                        null,
                        array(self::GROUP_DESIGN)
                    ),
                ),
                array(),
                false,
                null,
                array(self::GROUP_DESIGN)
            ),
            'aspect_ratio' => new ParameterDefinition\Choice(
                array(
                    'options' => $this->aspectRatios,
                ),
                true,
                null,
                array(self::GROUP_DESIGN)
            ),
            'show_details' => new ParameterDefinition\Compound\Boolean(
                array(
                    'show_details_on_hover' => new ParameterDefinition\Boolean(
                        array(),
                        false,
                        null,
                        array(self::GROUP_DESIGN)
                    ),
                ),
                array(),
                false,
                null,
                array(self::GROUP_DESIGN)
            ),
            'number_of_thumbnails' => new ParameterDefinition\Integer(
                array(
                    'min' => 1,
                ),
                true,
                null,
                array(self::GROUP_DESIGN)
            ),
            'enable_lightbox' => new ParameterDefinition\Boolean(
                array(),
                false,
                null,
                array(self::GROUP_DESIGN)
            ),
        ) + $this->getCommonParameters(array(self::GROUP_DESIGN));
    }

    /**
     * Returns if this block definition should have a collection.
     *
     * @return bool
     */
    public function hasCollection()
    {
        return true;
    }
}
