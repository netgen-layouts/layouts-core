<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;

class BlockDefinitionHandlerWithCompoundParameter extends BaseBlockDefinitionHandler
{
    /**
     * @var array
     */
    protected $parameterGroups = array();

    /**
     * Constructor.
     *
     * @param array $parameterGroups
     */
    public function __construct($parameterGroups = array())
    {
        $this->parameterGroups = $parameterGroups;
    }

    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        $compoundParam = new CompoundParameter('compound', new ParameterType\Compound\BooleanType(), array(), false, null, $this->parameterGroups);
        $compoundParam->setParameters(
            array(
                'inner' => new Parameter('inner', new ParameterType\TextLineType(), array(), false, null, $this->parameterGroups),
            )
        );

        return array(
            'css_class' => new Parameter('css_class', new ParameterType\TextLineType(), array(), false, null, $this->parameterGroups),
            'css_id' => new Parameter('css_id', new ParameterType\TextLineType(), array(), false, null, $this->parameterGroups),
            'compound' => $compoundParam,
        );
    }

    /**
     * Returns if this block definition should have a collection.
     *
     * @return bool
     */
    public function hasCollection()
    {
        return true;
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getDynamicParameters(Block $block, array $parameters = array())
    {
        return array('definition_param' => 'definition_value');
    }
}
