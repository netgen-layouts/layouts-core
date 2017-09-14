<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

class ExternalVideoHandler extends BlockDefinitionHandler
{
    /**
     * @var array
     */
    protected $services = array();

    public function __construct(array $services = array())
    {
        $this->services = array_flip($services);
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'service',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => $this->services,
            )
        );

        $builder->add(
            'video_id',
            ParameterType\TextLineType::class
        );

        $builder->add(
            'caption',
            ParameterType\TextLineType::class
        );
    }
}
