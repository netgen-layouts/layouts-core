<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

class CommonParametersPlugin extends Plugin
{
    /**
     * @var array
     */
    protected $defaultGroups = array();

    /**
     * Constructor.
     *
     * @param array $defaultGroups
     */
    public function __construct(array $defaultGroups = array())
    {
        $this->defaultGroups = $defaultGroups;
    }

    /**
     * Returns the fully qualified class name of the handler which this
     * plugin extends.
     *
     * @return string|string[]
     */
    public static function getExtendedHandler()
    {
        return BlockDefinitionHandlerInterface::class;
    }

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'css_class',
            ParameterType\TextLineType::class,
            array(
                'groups' => $this->defaultGroups,
                'label' => 'block.common_params.css_class',
            )
        );

        $builder->add(
            'css_id',
            ParameterType\TextLineType::class,
            array(
                'groups' => $this->defaultGroups,
                'label' => 'block.common_params.css_id',
            )
        );

        $builder->add(
            'set_container',
            ParameterType\BooleanType::class,
            array(
                'groups' => $this->defaultGroups,
                'label' => 'block.common_params.set_container',
            )
        );
    }
}
