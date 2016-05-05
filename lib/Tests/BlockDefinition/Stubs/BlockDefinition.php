<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Stubs;

use Netgen\BlockManager\BlockDefinition\BlockDefinition as BaseBlockDefinition;
use Symfony\Component\Validator\Constraints\NotBlank;
use Netgen\BlockManager\Parameters\Parameter;

class BlockDefinition extends BaseBlockDefinition
{
    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function getParameters()
    {
        return array(
            'css_id' => new Parameter\Text('CSS ID'),
            'css_class' => new Parameter\Text('CSS class', true),
        );
    }

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'block_definition';
    }

    /**
     * Returns the array specifying block parameter validator constraints.
     *
     * @return array
     */
    public function getParameterConstraints()
    {
        $constraints = parent::getParameterConstraints();
        $constraints['css_id'] = array(new NotBlank());

        return $constraints;
    }
}
