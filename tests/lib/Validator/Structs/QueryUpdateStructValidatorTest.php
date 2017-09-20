<?php

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType as QueryTypeStub;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandlerWithRequiredParameter;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Netgen\BlockManager\Validator\Structs\QueryUpdateStructValidator;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;

class QueryUpdateStructValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new QueryUpdateStructConstraint();

        $this->constraint->payload = new Query(
            array(
                'queryType' => new QueryTypeStub('query_type'),
            )
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
        return array(
            array(
                array(
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'locale' => null,
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'locale' => '',
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'locale' => 42,
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'locale' => 'nonexistent',
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'locale' => 'en',
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'locale' => 'en',
                    'parameterValues' => array(
                        'param' => '',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'locale' => 'en',
                    'parameterValues' => array(
                        'param' => null,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'locale' => 'en',
                    'parameterValues' => array(),
                ),
                true,
            ),
        );
    }

    /**
     * @return \Netgen\BlockManager\Collection\QueryType
     */
    private function getQueryType()
    {
        $handler = new QueryTypeHandlerWithRequiredParameter();

        return new QueryType(
            array(
                'type' => 'query_type',
                'handler' => new QueryTypeHandlerWithRequiredParameter(),
                'config' => $this->createMock(Configuration::class),
                'parameters' => $handler->getParameters(),
            )
        );
    }
}
