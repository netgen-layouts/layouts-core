<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input;

use Netgen\Layouts\Exception\Transfer\JsonValidationException;
use Netgen\Layouts\Transfer\Input\JsonValidator;
use PHPUnit\Framework\TestCase;

final class JsonValidatorTest extends TestCase
{
    private JsonValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new JsonValidator();
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\JsonValidator::parseJson
     * @covers \Netgen\Layouts\Transfer\Input\JsonValidator::validateJson
     */
    public function testValidateJson(): void
    {
        $this->validator->validateJson('{}', '{}');

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\JsonValidator::parseJson
     * @covers \Netgen\Layouts\Transfer\Input\JsonValidator::validateJson
     */
    public function testValidateJsonThrowsJsonValidationExceptionWithInvalidJson(): void
    {
        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage('JSON data failed to validate the schema');

        $this->validator->validateJson('{}', '{ "type": "array" }');
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\JsonValidator::parseJson
     * @covers \Netgen\Layouts\Transfer\Input\JsonValidator::validateJson
     */
    public function testValidateJsonThrowsJsonValidationExceptionWithNotAcceptableJson(): void
    {
        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage('Provided data is not an acceptable JSON string: Expected a JSON object, got string');

        $this->validator->validateJson('"abc"', '{ "type": "array" }');
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\JsonValidator::parseJson
     * @covers \Netgen\Layouts\Transfer\Input\JsonValidator::validateJson
     */
    public function testValidateJsonThrowsJsonValidationExceptionWithParseError(): void
    {
        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage('Provided data is not a valid JSON string: Syntax error (error code 4)');

        $this->validator->validateJson('INVALID', '{ "type": "array" }');
    }
}
