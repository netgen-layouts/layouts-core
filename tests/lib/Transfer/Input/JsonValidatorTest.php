<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Input;

use Netgen\BlockManager\Exception\Transfer\JsonValidationException;
use Netgen\BlockManager\Transfer\Input\JsonValidator;
use PHPUnit\Framework\TestCase;

final class JsonValidatorTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\JsonValidator
     */
    private $validator;

    public function setUp(): void
    {
        $this->validator = new JsonValidator();
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\JsonValidator::parseJson
     * @covers \Netgen\BlockManager\Transfer\Input\JsonValidator::validateJson
     */
    public function testValidateJson(): void
    {
        $this->validator->validateJson('{}', '{}');

        // Fake assertion to disable risky warning
        self::assertTrue(true);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\JsonValidator::parseJson
     * @covers \Netgen\BlockManager\Transfer\Input\JsonValidator::validateJson
     */
    public function testValidateJsonThrowsJsonValidationExceptionWithInvalidJson(): void
    {
        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage('JSON data failed to validate the schema');

        $this->validator->validateJson('{}', '{ "type": "array" }');
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\JsonValidator::parseJson
     * @covers \Netgen\BlockManager\Transfer\Input\JsonValidator::validateJson
     */
    public function testValidateJsonThrowsJsonValidationExceptionWithNotAcceptableJson(): void
    {
        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage('Provided data is not an acceptable JSON string: Expected a JSON object, got string');

        $this->validator->validateJson('"abc"', '{ "type": "array" }');
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\JsonValidator::parseJson
     * @covers \Netgen\BlockManager\Transfer\Input\JsonValidator::validateJson
     */
    public function testValidateJsonThrowsJsonValidationExceptionWithParseError(): void
    {
        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage('Provided data is not a valid JSON string: Syntax error (error code 4)');

        $this->validator->validateJson('INVALID', '{ "type": "array" }');
    }
}
