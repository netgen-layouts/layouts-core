<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Config;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Config\ConfigDefinitionInterface;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterList;
use Netgen\Layouts\Parameters\ParameterType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigStruct::class)]
final class ConfigStructTest extends TestCase
{
    private ConfigStruct $struct;

    protected function setUp(): void
    {
        $this->struct = new ConfigStruct();
    }

    public function testFillParametersFromConfig(): void
    {
        $configDefinition = $this->buildConfigDefinition();

        $compoundDefinition = $configDefinition->getParameterDefinition('compound');

        $config = Config::fromArray(
            [
                'definition' => $configDefinition,
                'parameters' => new ParameterList(
                    [
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
                ),
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
            $this->struct->parameterValues,
        );
    }

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
            $this->struct->parameterValues,
        );
    }

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
            $this->struct->parameterValues,
        );
    }

    private function buildConfigDefinition(): ConfigDefinitionInterface
    {
        $compoundDefinition = ParameterDefinition::fromArray(
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
            'compound' => $compoundDefinition,
        ];

        return ConfigDefinition::fromArray(['parameterDefinitions' => $parameterDefinitions]);
    }
}
