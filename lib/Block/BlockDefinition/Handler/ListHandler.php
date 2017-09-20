<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

class ListHandler extends BlockDefinitionHandler
{
    /**
     * @var array
     */
    private $columns = array();

    public function __construct(array $columns = array())
    {
        $this->columns = array_flip($columns);
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'number_of_columns',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => $this->columns,
                'groups' => array(self::GROUP_DESIGN),
            )
        );
    }
}
