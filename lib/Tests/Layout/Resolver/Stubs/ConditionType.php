<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;

class ConditionType implements ConditionTypeInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $matches = true;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param bool $matches
     */
    public function __construct($identifier, $matches = true)
    {
        $this->identifier = $identifier;
        $this->matches = $matches;
    }

    /**
     * Returns the condition type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the constraints that will be used to validate the condition value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return array();
    }

    /**
     * Returns if this condition matches the provided value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matches($value)
    {
        return $this->matches;
    }
}
