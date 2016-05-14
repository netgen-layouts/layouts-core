<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\Block\BlockDefinition as BaseBlockDefinition;
use Symfony\Component\Validator\Constraints\NotBlank;
use Netgen\BlockManager\Parameters\Parameter;

class BlockDefinition extends BaseBlockDefinition
{
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
