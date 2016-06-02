<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\ConditionMatcherInterface;

class ConditionMatcher implements ConditionMatcherInterface
{
    /**
     * @var bool
     */
    protected $matches = true;

    /**
     * Constructor.
     *
     * @param bool $matches
     */
    public function __construct($matches = true)
    {
        $this->matches = $matches;
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
