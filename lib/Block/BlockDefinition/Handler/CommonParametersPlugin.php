<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

/**
 * Block plugin which adds 3 parameters (css_class, css_id and set_container)
 * to every defined block.
 */
class CommonParametersPlugin extends Plugin
{
    /**
     * @var array
     */
    protected $defaultGroups = array();

    public function __construct(array $defaultGroups = array())
    {
        $this->defaultGroups = $defaultGroups;
    }

    public static function getExtendedHandler()
    {
        return BlockDefinitionHandlerInterface::class;
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'css_class',
            ParameterType\TextLineType::class,
            array(
                'groups' => $this->defaultGroups,
                'label' => 'block.common_params.css_class',
                'translatable' => false,
            )
        );

        $builder->add(
            'css_id',
            ParameterType\TextLineType::class,
            array(
                'groups' => $this->defaultGroups,
                'label' => 'block.common_params.css_id',
                'translatable' => false,
            )
        );

        $builder->add(
            'set_container',
            ParameterType\BooleanType::class,
            array(
                'groups' => $this->defaultGroups,
                'label' => 'block.common_params.set_container',
                'translatable' => false,
            )
        );
    }
}
