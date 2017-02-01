<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Placeholder as APIPlaceholder;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\ValueObject;

class Placeholder extends ValueObject implements APIPlaceholder
{
    use ParameterBasedValueTrait;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\API\Values\Page\Block[]
     */
    protected $blocks = array();

    /**
     * Returns the placeholder identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns all blocks in this placeholder.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}
