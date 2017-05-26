<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Core\Service\Validator\ConfigValidator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Validation;

class ConfigValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configValidatorMock;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\ConfigValidator
     */
    protected $configValidator;

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
     * @covers \Netgen\BlockManager\Core\Service\Validator\ConfigValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\ConfigValidator::validateConfigStructs
     * @dataProvider validateConfigStructDataProvider
     * @doesNotPerformAssertions
     */
    public function testValidateConfigStructs(array $config, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $configStructs = array();
        foreach ($config as $identifier => $configValues) {
            $configStructs[$identifier] = new ConfigStruct(
                array(
                    'parameterValues' => $configValues,
                )
            );
        }

        $this->configValidator->validateConfigStructs(
            $configStructs,
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\ConfigValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\ConfigValidator::validateConfigStructs
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     * @expectedExceptionMessage This value should be of type Netgen\BlockManager\API\Values\Config\ConfigStruct.
     * @doesNotPerformAssertions
     */
    public function testValidateConfigStructsWithInvalidStruct()
    {
        $this->configValidator->validateConfigStructs(
            array(
                'test' => new stdClass(),
            ),
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
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
                        'use_http_cache' => true,
                    ),
                    'test2' => array(
                        'use_http_cache' => true,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'test' => array(
                        'use_http_cache' => true,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'test2' => array(
                        'use_http_cache' => true,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'test' => array(
                        'use_http_cache' => true,
                    ),
                    'test2' => array(
                        'use_http_cache' => 42,
                    ),
                ),
                false,
            ),
        );
    }

    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    protected function getConfigDefinition($identifier)
    {
        $handler = new HttpCacheConfigHandler();

        return new ConfigDefinition('block', $identifier, $handler);
    }
}
