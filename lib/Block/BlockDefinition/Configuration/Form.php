<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Value;

final class Form extends Value
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $type;

    /**
     * Returns the form identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the form type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
