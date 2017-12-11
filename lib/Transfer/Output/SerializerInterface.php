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
interface SerializerInterface
{
    /**
     * Serializes the provided layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return array
     */
    public function serializeLayout(Layout $layout);

    /**
     * Serializes the provided rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return array
     */
    public function serializeRule(Rule $rule);
}
