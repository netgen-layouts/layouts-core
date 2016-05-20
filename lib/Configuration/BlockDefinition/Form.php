<?php

namespace Netgen\BlockManager\Configuration\BlockDefinition;

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
     * @var array
     */
    protected $parameters = array();

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param string $type
     * @param array $parameters
     */
    public function __construct($identifier, $type, array $parameters = array())
    {
        $this->identifier = $identifier;
        $this->type = $type;
        $this->parameters = $parameters;
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

    /**
     * Returns the block parameters this form will display.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
