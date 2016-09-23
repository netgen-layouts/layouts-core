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
     * Constructor.
     *
     * @param string $identifier
     * @param string $type
     * @param bool $enabled
     */
    public function __construct($identifier, $type, $enabled)
    {
        $this->identifier = $identifier;
        $this->type = $type;
        $this->enabled = $enabled;
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
}
