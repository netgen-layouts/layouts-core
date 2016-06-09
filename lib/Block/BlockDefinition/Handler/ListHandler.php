<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

class ListHandler extends BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
    /**
     * @var array
     */
    protected $columns = array();

    public function __construct(array $columns = array())
    {
        $this->columns = array_flip($columns);
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'number_of_columns' => new Parameter\Select(array('options' => $this->columns), true),
        ) + parent::getParameters();
    }
}
