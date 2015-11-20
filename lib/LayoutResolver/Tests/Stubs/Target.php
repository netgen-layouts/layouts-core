<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Stubs;

use Netgen\BlockManager\LayoutResolver\Rule\Target as BaseTarget;

class Target extends BaseTarget
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
     * Evaluates if values of this target match.
     *
     * @return bool
     */
    protected function evaluate()
    {
        return $this->matches;
    }
}
