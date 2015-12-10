<?php

namespace Netgen\BlockManager\BlockDefinition;

use Netgen\BlockManager\API\Values\Page\Block;

interface BlockDefinitionInterface
{
    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter[]
     */
    public function getParameters();

    /**
     * Returns the array specifying block parameter validator constraints.
     *
     * @return array
     */
    public function getParameterConstraints();

    /**
     * Returns the array of values provided by this block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getValues(Block $block, array $parameters = array());
}
