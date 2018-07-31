<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Input;

use Netgen\BlockManager\Exception\Transfer\JsonValidationException;
use stdClass;
use Swaggest\JsonSchema\Schema;
use Throwable;

final class JsonValidator implements JsonValidatorInterface
{
    public function validateJson(string $data, string $schema): void
    {
        $parsedSchema = $this->parseJson($schema);
        $parsedData = $this->parseJson($data);

        try {
            Schema::import($parsedSchema)->in($parsedData);
        } catch (Throwable $t) {
            throw JsonValidationException::validationFailed($t->getMessage(), $t);
        }
    }

    /**
     * Parses JSON data.
     *
     * @throws \Netgen\BlockManager\Exception\Transfer\JsonValidationException
     */
    private function parseJson(string $data): stdClass
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
