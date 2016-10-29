<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterDefinition;

class ListHandler extends BlockDefinitionHandler
{
    /**
     * @var array
     */
    protected $columns = array();

    /**
     * Constructor.
     *
     * @param array $columns
     */
    public function __construct(array $columns = array())
    {
        $this->columns = array_flip($columns);
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters()
    {
        return array(
            'number_of_columns' => new ParameterDefinition\Choice(
                array(
                    'options' => $this->columns,
                ),
                true,
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
