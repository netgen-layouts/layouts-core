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

    /**
     * Constructor.
     *
     * @param array $services
     */
    public function __construct(array $services = array())
    {
        $this->services = array_flip($services);
    }

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
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
