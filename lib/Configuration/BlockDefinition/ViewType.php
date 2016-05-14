<?php

namespace Netgen\BlockManager\Configuration\BlockDefinition;

class ViewType
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param string $name
     */
    public function __construct($identifier, $name)
    {
        $this->identifier = $identifier;
        $this->name = $name;
    }

    /**
     * Returns the view type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the view type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
