<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Config;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use PHPUnit\Framework\TestCase;

final class ConfigStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigStruct
     */
    private $struct;

    public function setUp(): void
    {
        $this->struct = new ConfigStruct();
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigStruct::fillParameters
     */
    public function testFillParameters(): void
    {
        $configDefinition = $this->buildConfigDefinition();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillParameters($configDefinition, $initialValues);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigStruct::fillParameters
     */
    public function testFillParametersWithMissingValues(): void
    {
        $configDefinition = $this->buildConfigDefinition();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParameters($configDefinition, $initialValues);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigStruct::fillParametersFromConfig
     */
    public function testFillParametersFromConfig(): void
    {
        $configDefinition = $this->buildConfigDefinition();

        /** @var \Netgen\BlockManager\Parameters\CompoundParameterDefinition $compoundDefinition */
        $compoundDefinition = $configDefinition->getParameterDefinition('compound');

        $config = new Config(
            [
                'definition' => $configDefinition,
                'parameters' => [
                    'css_class' => new Parameter(
                        [
                            'value' => 'css',
                            'parameterDefinition' => $configDefinition->getParameterDefinition('css_class'),
                        ]
                    ),
                    'inner' => new Parameter(
                        [
                            'value' => 'inner',
                            'parameterDefinition' => $compoundDefinition->getParameterDefinition('inner'),
                        ]
                    ),
                ],
            ]
        );

        $this->struct->fillParametersFromConfig($config);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigStruct::fillParametersFromHash
     */
    public function testFillParametersFromHash(): void
    {
        $configDefinition = $this->buildConfigDefinition();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($configDefinition, $initialValues);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigStruct::fillParametersFromHash
     */
    public function testFillParametersFromHashWithMissingValues(): void
    {
        $configDefinition = $this->buildConfigDefinition();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($configDefinition, $initialValues);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    private function buildConfigDefinition(): ConfigDefinitionInterface
    {
        $compoundParameter = new CompoundParameterDefinition(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
                'parameterDefinitions' => [
                    'inner' => new ParameterDefinition(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'defaultValue' => 'inner_default',
                        ]
                    ),
                ],
            ]
        );

        $parameterDefinitions = [
            'css_class' => new ParameterDefinition(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'css_default',
                ]
            ),
            'css_id' => new ParameterDefinition(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'id_default',
                ]
            ),
            'compound' => $compoundParameter,
        ];

        return new ConfigDefinition(['parameterDefinitions' => $parameterDefinitions]);
    }
}
