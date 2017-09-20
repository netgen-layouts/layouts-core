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
    private $tags = array();

    /**
     * @var array
     */
    private $linkValueTypes = array();

    public function __construct(array $tags = array(), array $linkValueTypes = array())
    {
        $this->tags = array_flip($tags);
        $this->linkValueTypes = $linkValueTypes;
    }

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
