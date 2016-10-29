<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterDefinition;

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
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters()
    {
        return array(
            'tag' => new ParameterDefinition\Choice(
                array('options' => $this->options),
                true
            ),
            'title' => new ParameterDefinition\TextLine(array(), true),
            'link' => new ParameterDefinition\Link(
                array(
                    'value_types' => $this->linkValueTypes,
                )
            ),
        ) + $this->getCommonParameters();
    }
}
