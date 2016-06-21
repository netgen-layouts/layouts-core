<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;

class TargetType implements TargetTypeInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param mixed $value
     */
    public function __construct($identifier, $value)
    {
        $this->identifier = $identifier;
        $this->value = $value;
    }

    /**
     * Returns the target type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Provides the value for the target to be used in matching process.
     *
     * @return mixed
     */
    public function provideValue()
    {
        return $this->value;
    }
}
