<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

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
     * Serializes the layouts with provided UUIDs.
     *
     * @param string[] $layoutIds
     *
     * @return array
     */
    public function serializeLayouts(array $layoutIds): array;

    /**
     * Serializes the rules with provided UUIDs.
     *
     * @param string[] $ruleIds
     *
     * @return array
     */
    public function serializeRules(array $ruleIds): array;
}
