<?php

namespace Netgen\BlockManager\Transfer\Input;

use Exception;
use Netgen\BlockManager\Exception\Transfer\JsonValidationException;
use stdClass;
use Swaggest\JsonSchema\Schema;

class JsonValidator implements JsonValidatorInterface
{
    public function validateJson($data, $schema)
    {
        $schema = $this->parseJson($schema);
        $data = $this->parseJson($data);

        try {
            $schema = Schema::import($schema);
            $schema->in($data);
        } catch (Exception $e) {
            throw JsonValidationException::validationFailed($e->getMessage(), $e);
        }
    }

    /**
     * Parses JSON data.
     *
     * @param string $data
     *
     * @throws \Netgen\BlockManager\Exception\Transfer\JsonValidationException
     *
     * @return \stdClass
     */
    private function parseJson($data)
    {
        $data = json_decode($data);

        if ($data instanceof stdClass) {
            return $data;
        }

        $errorCode = json_last_error();

        if ($errorCode !== JSON_ERROR_NONE) {
            throw JsonValidationException::parseError(json_last_error_msg(), $errorCode);
        }

        throw JsonValidationException::notAcceptable(
            sprintf('Expected a JSON object, got %s', gettype($data))
        );
    }
}
