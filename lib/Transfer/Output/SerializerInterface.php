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
     * Serializes the entities with provided UUIDs.
     *
     * @param string[] $entityIds
     *
     * @return array<string, mixed>
     */
    public function serialize(string $type, array $entityIds): array;
}
