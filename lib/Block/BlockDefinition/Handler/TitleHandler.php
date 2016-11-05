<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

class TitleHandler extends BlockDefinitionHandler
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var array
     */
    protected $linkValueTypes = array();

    /**
     * Constructor.
     *
     * @param array $options
     * @param array $linkValueTypes
     */
    public function __construct(array $options = array(), array $linkValueTypes = array())
    {
        $this->options = array_flip($options);
        $this->linkValueTypes = $linkValueTypes;
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'tag' => new Parameter\Choice(
                array('options' => $this->options),
                true
            ),
            'title' => new Parameter\TextLine(array(), true),
            'use_link' => new Parameter\Compound\Boolean(
                array(
                    'link' => new Parameter\Link(
                        array(
                            'value_types' => $this->linkValueTypes,
                        )
                    ),
                )
            )
        ) + $this->getCommonParameters();
    }
}
