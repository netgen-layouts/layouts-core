<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Input;

interface JsonValidatorInterface
{
    /**
     * Validates the provided JSON against the schema.
     *
     * @param string $data
     * @param string $schema
     *
     * @throws \Netgen\BlockManager\Exception\Transfer\JsonValidationException If the JSON validation failed
     */
    public function validateJson($data, $schema);
}
