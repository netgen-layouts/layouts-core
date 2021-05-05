<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Config;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Config\ConfigDefinitionInterface;
use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use PHPUnit\Framework\TestCase;

final class ConfigStructTest extends TestCase
{
    private ConfigStruct $struct;

    protected function setUp(): void
    {
        $this->struct = new ConfigStruct();
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigStruct::fillParametersFromConfig
     */
    public function testFillParametersFromConfig(): void
    {
        $configDefinition = $this->buildConfigDefinition();

        /** @var \Netgen\Layouts\Parameters\CompoundParameterDefinition $compoundDefinition */
        $compoundDefinition = $configDefinition->getParameterDefinition('compound');

        $config = Config::fromArray(
            [
                'definition' => $configDefinition,
                'parameters' => [
                    'css_class' => Parameter::fromArray(
                        [
                            'value' => 'css',
                            'parameterDefinition' => $configDefinition->getParameterDefinition('css_class'),
                        ],
                    ),
                    'inner' => Parameter::fromArray(
                        [
                            'value' => 'inner',
                            'parameterDefinition' => $compoundDefinition->getParameterDefinition('inner'),
                        ],
                    ),
                ],
            ],
        );

        $this->struct->fillParametersFromConfig($config);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigStruct::fillParametersFromHash
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

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigStruct::fillParametersFromHash
     */
    public function testFillParametersFromHashWithMissingValues(): void
    {
        $configDefinition = $this->buildConfigDefinition();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($configDefinition, $initialValues);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    private function buildConfigDefinition(): ConfigDefinitionInterface
    {
        $compoundParameter = CompoundParameterDefinition::fromArray(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'isRequired' => false,
                'defaultValue' => true,
                'parameterDefinitions' => [
                    'inner' => ParameterDefinition::fromArray(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'isRequired' => false,
                            'defaultValue' => 'inner_default',
                        ],
                    ),
                ],
            ],
        );

        $parameterDefinitions = [
            'css_class' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => false,
                    'defaultValue' => 'css_default',
                ],
            ),
            'css_id' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => false,
                    'defaultValue' => 'id_default',
                ],
            ),
            'compound' => $compoundParameter,
        ];

        return ConfigDefinition::fromArray(['parameterDefinitions' => $parameterDefinitions]);
    }
}
