<?php

namespace Netgen\BlockManager\Transfer\Output;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;

/**
 * Serializer serializes domain entity into hash representation, which can be
 * transferred through a plain text format, like JSON or XML.
 *
 * Hash format is either a scalar value, a hash array (associative array),
 * a pure numeric array or a nested combination of these.
 */
final class Serializer
{
    /**
     * @var \Netgen\BlockManager\Transfer\Output\Visitor
     */
    private $visitor;

    /**
     * @param \Netgen\BlockManager\Transfer\Output\Visitor $visitor
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
