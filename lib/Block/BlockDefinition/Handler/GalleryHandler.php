<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

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
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'next_and_previous' => new Parameter\Boolean(),
            'show_pagination' => new Parameter\Compound\Boolean(
                array(
                    'pagination_type' => new Parameter\Choice(array('options' => $this->paginationTypes), true),
                )
            ),
            'infinite_loop' => new Parameter\Boolean(),
            'transition' => new Parameter\Choice(array('options' => $this->transitions), true),
            'autoplay' => new Parameter\Compound\Boolean(
                array(
                    'autoplay_time' => new Parameter\Range(
                        array('min' => $this->minAutoplayTime, 'max' => $this->maxAutoplayTime),
                        true
                    ),
                )
            ),
            'aspect_ratio' => new Parameter\Choice(array('options' => $this->aspectRatios), true),
            'show_details' => new Parameter\Compound\Boolean(
                array(
                    'show_details_on_hover' => new Parameter\Boolean(),
                )
            ),
            'number_of_thumbnails' => new Parameter\Integer(array('min' => 1), true),
            'enable_lightbox' => new Parameter\Boolean(),
        ) + $this->getCommonParameters();
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
