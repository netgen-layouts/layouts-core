<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class BlockDefinitionHandlerWithTranslatableCompoundParameter extends BaseBlockDefinitionHandler
{
    /**
     * @var array
     */
    private $parameterGroups = array();

    public function __construct($parameterGroups = array())
    {
        $this->parameterGroups = $parameterGroups;
    }

    public function getParameterDefinitions()
    {
        $compoundParam = new CompoundParameterDefinition(
            array(
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'groups' => $this->parameterGroups,
                'options' => array(
                    'translatable' => true,
                ),
                'parameterDefinitions' => array(
                    'inner' => new ParameterDefinition(
                        array(
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'groups' => $this->parameterGroups,
                            'options' => array(
                                'translatable' => true,
                            ),
                        )
                    ),
                ),
            )
        );

        return array(
            'css_class' => new ParameterDefinition(
                array(
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'some-class',
                    'groups' => $this->parameterGroups,
                    'options' => array(
                        'translatable' => true,
                    ),
                )
            ),
            'css_id' => new ParameterDefinition(
                array(
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'groups' => $this->parameterGroups,
                    'options' => array(
                        'translatable' => false,
                    ),
                )
            ),
            'compound' => $compoundParam,
        );
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
        $params['definition_param'] = 'definition_value';
    }
}
