<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\Block\BlockDefinition as BaseBlockDefinition;
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
}
