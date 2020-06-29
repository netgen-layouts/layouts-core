<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input;

use JsonException;
use Netgen\Layouts\Exception\Transfer\JsonValidationException;
use stdClass;
use Swaggest\JsonSchema\Schema;
use Throwable;
use function get_debug_type;
use function json_decode;
use function sprintf;
use const JSON_THROW_ON_ERROR;

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
     * @throws \Netgen\Layouts\Exception\Transfer\JsonValidationException
     */
    private function parseJson(string $data): stdClass
    {
        try {
            $data = json_decode($data, false, 512, JSON_THROW_ON_ERROR);

            if (!$data instanceof stdClass) {
                throw JsonValidationException::notAcceptable(
                    sprintf('Expected a JSON object, got %s', get_debug_type($data))
                );
            }

            return $data;
        } catch (JsonException $e) {
            throw JsonValidationException::parseError($e->getMessage(), $e->getCode());
        }
    }
}
