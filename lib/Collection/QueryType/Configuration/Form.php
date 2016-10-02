<?php

namespace Netgen\BlockManager\Collection\QueryType\Configuration;

class Form
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
     * Constructor.
     *
     * @param string $identifier
     * @param string $type
     */
    public function __construct($identifier, $type)
    {
        $this->identifier = $identifier;
        $this->type = $type;
    }

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
