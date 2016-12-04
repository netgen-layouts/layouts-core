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
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
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

        $this->buildCommonParameters($builder, array(self::GROUP_DESIGN));
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
