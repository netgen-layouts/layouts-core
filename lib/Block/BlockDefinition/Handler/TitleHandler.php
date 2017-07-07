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
    protected $tags = array();

    /**
     * @var array
     */
    protected $linkValueTypes = array();

    /**
     * Constructor.
     *
     * @param array $tags
     * @param array $linkValueTypes
     */
    public function __construct(array $tags = array(), array $linkValueTypes = array())
    {
        $this->tags = array_flip($tags);
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
                'options' => $this->tags,
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
    }
}
