<?php

namespace Netgen\BlockManager\Layout\Container;

use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\ValueObject;

class PlaceholderDefinition extends ValueObject implements PlaceholderDefinitionInterface
{
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * Returns placeholder identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
