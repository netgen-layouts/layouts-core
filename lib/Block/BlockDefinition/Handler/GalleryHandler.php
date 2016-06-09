<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

class GalleryHandler extends BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
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

    public function __construct(
        array $paginationTypes = array(),
        array $transitions = array(),
        array $aspectRatios = array()
    ) {
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
                    'pagination_type' => new Parameter\Select(array('options' => $this->paginationTypes), true),
                )
            ),
            'infinite_loop' => new Parameter\Boolean(),
            'transition' => new Parameter\Select(array('options' => $this->transitions), true),
            'autoplay' => new Parameter\Compound\Boolean(
                array(
                    'autoplay_time' => new Parameter\Text(array(), true),
                )
            ),
            'aspect_ratio' => new Parameter\Select(array('options' => $this->aspectRatios), true),
            'show_details' => new Parameter\Compound\Boolean(
                array(
                    'show_details_on_hover' => new Parameter\Boolean(),
                )
            ),
            'number_of_thumbnails' => new Parameter\Integer(array('min' => 1), true),
            'enable_lightbox' => new Parameter\Boolean(),
        ) + parent::getParameters();
    }
}
