<?php

namespace Netgen\BlockManager\LayoutResolver;

use Netgen\BlockManager\LayoutResolver\Rule\TargetInterface;

class Rule
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface
     */
    public $target;

    /**
     * Constructor.
     *
     * @param int|string $layoutId
     * @param \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface $target
     */
    public function __construct($layoutId, TargetInterface $target)
    {
        $this->layoutId = $layoutId;
        $this->target = $target;
    }
}
