<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Stubs;

use Netgen\BlockManager\BlockDefinition\BlockDefinition as BaseBlockDefinition;
use Netgen\BlockManager\API\Values\Page\Block;
use Symfony\Component\Validator\Constraints\NotBlank;

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
     * Returns block definition human readable name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Block definition';
    }

    /**
     * Returns the array of values provided by this block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return array
     */
    public function getValues(Block $block)
    {
        return array();
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
