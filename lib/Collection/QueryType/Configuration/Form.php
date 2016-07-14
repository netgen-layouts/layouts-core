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
     * @var bool
     */
    protected $enabled;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param string $type
     * @param bool $enabled
     * @param array $parameters
     */
    public function __construct($identifier, $type, $enabled, array $parameters = null)
    {
        $this->identifier = $identifier;
        $this->type = $type;
        $this->enabled = $enabled;
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
     * Returns if the form is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns the query parameters this form will display.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
