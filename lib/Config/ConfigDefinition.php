<?php

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\ValueObject;

class ConfigDefinition extends ValueObject implements ConfigDefinitionInterface
{
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * Returns the type of the config definition.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns config definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
