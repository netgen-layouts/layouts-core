<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

class TitleHandler extends BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
    /**
     * @var array
     */
    protected $options = array(
        'Heading 1' => 'h1',
        'Heading 2' => 'h2',
        'Heading 3' => 'h3',
    );

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'tag' => new Parameter\Select(
                array('options' => $this->options),
                true
            ),
            'title' => new Parameter\Text(array(), true),
        ) + parent::getParameters();
    }
}
