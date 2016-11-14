<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

class LinkHandler extends BlockDefinitionHandler
{
    /**
     * @var array
     */
    protected $linkStyles = array();

    /**
     * @var array
     */
    protected $linkValueTypes = array();

    /**
     * Constructor.
     *
     * @param array $linkStyles
     * @param array $linkValueTypes
     */
    public function __construct(array $linkStyles = array(), array $linkValueTypes = array())
    {
        $this->linkStyles = array_flip($linkStyles);
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
            'link_text',
            ParameterType\TextLineType::class,
            array(
                'required' => true,
                'default_value' => 'Text',
            )
        );

        $builder->add(
            'link_style',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => $this->linkStyles,
            )
        );

        $builder->add(
            'link',
            ParameterType\LinkType::class,
            array(
                'value_types' => $this->linkValueTypes,
            )
        );

        $this->buildCommonParameters($builder);
    }
}
