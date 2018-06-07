<?php

namespace Netgen\BlockManager\Tests\Transfer\Input;

use Netgen\BlockManager\Transfer\Input\JsonValidator;
use PHPUnit\Framework\TestCase;

final class JsonValidatorTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\JsonValidator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new JsonValidator();
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\JsonValidator::validateJson
     */
    public function testValidateJson()
    {
        $this->assertNull($this->validator->validateJson('{}', '{}'));
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\JsonValidator::validateJson
     * @expectedException \Netgen\BlockManager\Exception\Transfer\JsonValidationException
     * @expectedExceptionMessage JSON data failed to validate the schema
     */
    public function testValidateJsonThrowsJsonValidationException()
    {
        $this->validator->validateJson('{}', '{ "type": "array" }');
    }
}
