<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;

final class BlockDefinitionHandlerWithRequiredParameter extends BaseBlockDefinitionHandler
{
    /**
     * Returns the array specifying block parameter definitions.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameterDefinitions()
    {
        return array(
            'css_class' => new ParameterDefinition(
                array(
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => true,
                    'options' => array(
                        'translatable' => false,
                    ),
                ),
                true
            ),
            'css_id' => new ParameterDefinition(
                array(
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'options' => array(
                        'translatable' => false,
                    ),
                ),
                true
            ),
        );
    }

    /**
     * Adds the dynamic parameters to the $params object for the provided block.
     *
     * @param \Netgen\BlockManager\Block\DynamicParameters $params
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     */
    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
        $params['definition_param'] = 'definition_value';
    }
}
