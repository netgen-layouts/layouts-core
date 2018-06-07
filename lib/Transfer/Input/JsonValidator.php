<?php

namespace Netgen\BlockManager\Transfer\Input;

use Exception;
use Netgen\BlockManager\Exception\Transfer\JsonValidationException;
use Swaggest\JsonSchema\Schema;

class JsonValidator implements JsonValidatorInterface
{
    /**
     * Validates the provided JSON against the schema.
     *
     * @param string $json
     * @param string $schema
     */
    public function validateJson($json, $schema)
    {
        try {
            $schema = Schema::import(json_decode($schema));
            $schema->in(json_decode($json));
        } catch (Exception $e) {
            throw JsonValidationException::validationFailed($e->getMessage(), $e);
        }
    }
}
