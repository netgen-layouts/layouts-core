<?php

namespace Netgen\BlockManager\Transfer\Serializer;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Transfer\Serializer;

/**
 * Native implementation of the Serializer.
 */
class Native extends Serializer
{
    /**
     * @var \Netgen\BlockManager\Transfer\Serializer\Visitor
     */
    private $visitor;

    /**
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $visitor
     */
    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor;
    }

    public function serializeLayout(Layout $layout)
    {
        return $this->visitor->visit($layout);
    }

    public function serializeRule(Rule $rule)
    {
        return $this->visitor->visit($rule);
    }
}
