<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

abstract class BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
    const GROUP_CONTENT = 'content';
    const GROUP_DESIGN = 'design';

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getDynamicParameters(Block $block, array $parameters = array())
    {
        return array();
    }

    /**
     * Returns if this block definition should have a collection.
     *
     * @return bool
     */
    public function hasCollection()
    {
        return false;
    }

    /**
     * Builds the parameters most blocks will use by using provided parameter builder.
     *
     * @param array $groups
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildCommonParameters(ParameterBuilderInterface $builder, array $groups = array())
    {
        $builder->add(
            'css_class',
            ParameterType\TextLineType::class,
            array(
                'groups' => $groups,
            )
        );

        $builder->add(
            'css_id',
            ParameterType\TextLineType::class,
            array(
                'groups' => $groups,
            )
        );

        $builder->add(
            'set_container',
            ParameterType\BooleanType::class,
            array(
                'groups' => $groups,
            )
        );
    }
}
