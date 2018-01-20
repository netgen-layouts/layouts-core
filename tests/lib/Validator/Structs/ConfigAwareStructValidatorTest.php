<?php

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ConfigAwareStructValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new ConfigAwareStructConstraint();

        $this->constraint->payload = new Block(
            array(
                'configs' => array(
                    'http_cache' => new Config(
                        array(
                            'configKey' => 'http_cache',
                            'definition' => new ConfigDefinition(
                                'http_cache',
                                new HttpCacheConfigHandler()
                            ),
                        )
                    ),
                ),
            )
        );

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new ConfigAwareStructValidator();
    }

    /**
     * @param array $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, new BlockUpdateStruct($value));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\Config\ConfigAwareValue", "stdClass" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidPayload()
    {
        $this->constraint->payload = new stdClass();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\ConfigAwareStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\Config\ConfigAwareStruct", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->constraint->payload = new Block();
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array(
                array(
                    'configStructs' => array(
                        'http_cache' => new ConfigStruct(
                            array(
                                'parameterValues' => array(
                                    'use_http_cache' => true,
                                    'shared_max_age' => 300,
                                ),
                            )
                        ),
                        'other' => new ConfigStruct(
                            array(
                                'parameterValues' => array(
                                    'use_http_cache' => false,
                                    'shared_max_age' => null,
                                ),
                            )
                        ),
                    ),
                ),
                true,
            ),
            array(
                array(
                    'configStructs' => array(
                        'http_cache' => new ConfigStruct(
                            array(
                                'parameterValues' => array(
                                    'use_http_cache' => null,
                                    'shared_max_age' => 300,
                                ),
                            )
                        ),
                    ),
                ),
                true,
            ),
            array(
                array(
                    'configStructs' => array(
                        'http_cache' => new ConfigStruct(
                            array(
                                'parameterValues' => array(
                                    'use_http_cache' => 42,
                                    'shared_max_age' => 300,
                                ),
                            )
                        ),
                    ),
                ),
                false,
            ),
            array(
                array(
                    'configStructs' => array(
                        'http_cache' => new ConfigStruct(
                            array(
                                'parameterValues' => array(
                                    'shared_max_age' => 300,
                                ),
                            )
                        ),
                    ),
                ),
                true,
            ),
            array(
                array(
                    'configStructs' => array(
                        'http_cache' => new ConfigStruct(
                            array(
                                'parameterValues' => array(
                                    'use_http_cache' => true,
                                    'shared_max_age' => null,
                                ),
                            )
                        ),
                    ),
                ),
                true,
            ),
            array(
                array(
                    'configStructs' => array(
                        'http_cache' => new ConfigStruct(
                            array(
                                'parameterValues' => array(
                                    'use_http_cache' => true,
                                    'shared_max_age' => '42',
                                ),
                            )
                        ),
                    ),
                ),
                false,
            ),
            array(
                array(
                    'configStructs' => array(
                        'http_cache' => new ConfigStruct(
                            array(
                                'parameterValues' => array(
                                    'use_http_cache' => true,
                                ),
                            )
                        ),
                    ),
                ),
                true,
            ),
        );
    }
}
