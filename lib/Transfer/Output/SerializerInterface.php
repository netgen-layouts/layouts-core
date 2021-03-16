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
     * @param array<string, string> $entityIds The list of entities to serialize. Keys should be
     *                                         entity IDs, and values should be the type of entity for provided ID.
     *
     * @return array<string, mixed>
     */
    public function serialize(array $entityIds): array;
}
