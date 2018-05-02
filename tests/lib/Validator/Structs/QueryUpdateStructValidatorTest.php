<?php

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;

final class QueryUpdateStructValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new QueryUpdateStructConstraint();

        $this->constraint->payload = new Query(
            [
                'queryType' => new QueryType('query_type'),
            ]
        );

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new QueryUpdateStructValidator();
    }

    /**
     * @param array $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, new QueryUpdateStruct($value));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new QueryUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\Collection\Query", "stdClass" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlock()
    {
        $this->constraint->payload = new stdClass();
        $this->assertValid(true, new QueryUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->constraint->payload = new Query();
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return [
            [
                [
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => null,
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => '',
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'nonexistent',
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en-US',
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en_US.utf8',
                    'parameterValues' => [
                        'param' => 'value',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'parameterValues' => [
                        'param' => '',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'parameterValues' => [
                        'param' => null,
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'parameterValues' => [],
                ],
                true,
            ],
        ];
    }
}
