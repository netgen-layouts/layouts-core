<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Input;

interface JsonValidatorInterface
{
    /**
     * Validates the provided JSON against the schema.
     *
     * @throws \Netgen\BlockManager\Exception\Transfer\JsonValidationException If the JSON validation failed
     */
    public function validateJson(string $data, string $schema): void;
}
