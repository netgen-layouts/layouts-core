<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

class ButtonHandler extends BlockDefinitionHandler
{
    /**
     * @var array
     */
    protected $styles = array();

    /**
     * @var array
     */
    protected $valueTypes = array();

    /**
     * Constructor.
     *
     * @param array $styles
     * @param array $valueTypes
     */
    public function __construct(array $styles = array(), array $valueTypes = array())
    {
        $this->styles = array_flip($styles);
        $this->valueTypes = $valueTypes;
    }

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'text',
            ParameterType\TextLineType::class,
            array(
                'required' => true,
                'default_value' => 'Text',
            )
        );

        $builder->add(
            'style',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => $this->styles,
            )
        );

        $builder->add(
            'link',
            ParameterType\LinkType::class,
            array(
                'value_types' => $this->valueTypes,
            )
        );

        $this->buildCommonParameters($builder);
    }
}
