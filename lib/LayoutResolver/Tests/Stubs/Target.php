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
     * Returns if this target matches.
     *
     * @return bool
     */
    public function matches()
    {
        return $this->matches;
    }
}
