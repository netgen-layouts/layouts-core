<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Core\Service\Validator\ConfigValidator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Validation;

final class ConfigValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\ConfigValidator
     */
    private $configValidator;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->configValidator = new ConfigValidator();
        $this->configValidator->setValidator($this->validator);
    }

    /**
     * @param array $config
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\ConfigValidator::validateConfigStructs
     * @dataProvider validateConfigStructDataProvider
     */
    public function testValidateConfigStructs(array $config, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $configStructs = array();
        foreach ($config as $configKey => $configValues) {
            $configStructs[$configKey] = new ConfigStruct(
                array(
                    'parameterValues' => $configValues,
                )
            );
        }

        $this->configValidator->validateConfigStructs(
            $configStructs,
            array(
                'test' => new ConfigDefinition('test'),
                'test2' => new ConfigDefinition('test2'),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\ConfigValidator::validateConfigStructs
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     * @expectedExceptionMessage This value should be of type Netgen\BlockManager\API\Values\Config\ConfigStruct.
     */
    public function testValidateConfigStructsWithInvalidStruct()
    {
        $this->configValidator->validateConfigStructs(
            array(
                'test' => new stdClass(),
            ),
            array(
                'test' => new ConfigDefinition('test'),
                'test2' => new ConfigDefinition('test2'),
            )
        );
    }

    public function validateConfigStructDataProvider()
    {
        return array(
            array(
                array(),
                true,
            ),
            array(
                array(
                    'test' => array(
                        'param' => 'value',
                    ),
                    'test2' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'test' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'test2' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'test' => array(
                        'param' => 'value',
                    ),
                    'test2' => array(
                        'param' => 42,
                    ),
                ),
                false,
            ),
            array(
                array(
                    'test' => array(
                        'param' => 'value',
                    ),
                    'unknown' => array(),
                ),
                false,
            ),
        );
    }
}
