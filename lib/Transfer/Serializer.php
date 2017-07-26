<?php

namespace Netgen\BlockManager\Transfer;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;

/**
 * Serializer serializes Value object into hash representation, which can be
 * transferred through a plain text format, like JSON or XML.
 *
 * Hash format is either a scalar value, a hash array (associative array),
 * a pure numeric array or a nested combination of these.
 */
abstract class Serializer
{
    /**
     * Serialize given $layout into hash format.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return mixed
     */
    abstract public function serializeLayout(Layout $layout);

    /**
     * Serialize given $rule into hash format.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return mixed
     */
    abstract public function serializeRule(Rule $rule);
}
