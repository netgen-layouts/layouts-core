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
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = array_flip($options);
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
        ) + parent::getParameters();
    }
}
