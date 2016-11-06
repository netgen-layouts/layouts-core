<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

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
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'tag',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => $this->options,
            )
        );

        $builder->add(
            'title',
            ParameterType\TextLineType::class,
            array(
                'required' => true,
                'default_value' => 'Title',
            )
        );

        $builder->add(
            'use_link',
            ParameterType\Compound\BooleanType::class
        );

        $builder->get('use_link')->add(
            'link',
            ParameterType\LinkType::class,
            array(
                'value_types' => $this->linkValueTypes,
            )
        );

        $this->buildCommonParameters($builder);
    }
}
