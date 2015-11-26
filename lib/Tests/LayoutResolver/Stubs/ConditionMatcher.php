<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Stubs;

use Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface;

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
     * Returns the unique identifier of the condition this matcher matches.
     *
     * @return string
     */
    public function getConditionIdentifier()
    {
        return 'condition';
    }

    /**
     * Returns if this condition matches provided parameters.
     *
     * @param array $parameters
     *
     * @return bool
     */
    public function matches(array $parameters)
    {
        return $this->matches;
    }
}
