<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry;
use Netgen\BlockManager\Core\Service\Validator\ConfigValidator;
use Netgen\BlockManager\Exception\Validation\ValidationFailedException;
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

        $configDefinitionRegistry = new ConfigDefinitionRegistry();

        $configDefinitionRegistry->addConfigDefinition(
            'block',
            'test',
            $this->getConfigDefinition('test')
        );

        $configDefinitionRegistry->addConfigDefinition(
            'block',
            'test2',
            $this->getConfigDefinition('test2')
        );

        $configDefinitionRegistry->addConfigDefinition(
            'block',
            'test3',
            $this->getConfigDefinition('test3')
        );

        $this->configValidator = new ConfigValidator($configDefinitionRegistry);
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
            $this->expectException(ValidationFailedException::class);
        }

        $configStructs = array();
        foreach ($config as $identifier => $configValues) {
            $configStructs[$identifier] = new ConfigStruct(
                array(
                    'parameterValues' => $configValues,
                )
            );
        }

        $this->configValidator->validateConfigStructs('block', $configStructs);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\ConfigValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\ConfigValidator::validateConfigStructs
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationFailedException
     * @expectedExceptionMessage This value should be of type Netgen\BlockManager\API\Values\Config\ConfigStruct.
     * @doesNotPerformAssertions
     */
    public function testValidateConfigStructsWithInvalidStruct()
    {
        $this->configValidator->validateConfigStructs(
            'block',
            array(
                'test' => new stdClass(),
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
            array(
                array(
                    'test4' => array(
                        'use_http_cache' => true,
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
