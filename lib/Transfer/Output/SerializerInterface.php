<?php

namespace Netgen\BlockManager\Transfer\Output;

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
     * Serializes the layouts with provided IDs.
     *
     * @param array $layoutIds
     *
     * @return array
     */
    public function serializeLayouts(array $layoutIds);

    /**
     * Serializes the rules with provided IDs.
     *
     * @param array $ruleIds
     *
     * @return array
     */
    public function serializeRules(array $ruleIds);
}
