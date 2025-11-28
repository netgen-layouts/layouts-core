<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterList;
use Netgen\Layouts\Parameters\ParameterType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockUpdateStruct::class)]
final class BlockUpdateStructTest extends TestCase
{
    private BlockUpdateStruct $struct;

    protected function setUp(): void
    {
        $this->struct = new BlockUpdateStruct();
    }

    public function testFillParametersFromBlock(): void
    {
        $blockDefinition = $this->buildBlockDefinition();

        $compoundDefinition = $blockDefinition->getParameterDefinition('compound');

        $block = Block::fromArray(
            [
                'definition' => $blockDefinition,
                'parameters' => new ParameterList(
                    [
                        'css_class' => Parameter::fromArray(
                            [
                                'value' => 'css',
                                'parameterDefinition' => $blockDefinition->getParameterDefinition('css_class'),
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

        $this->struct->fillParametersFromBlock($block);

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
        $blockDefinition = $this->buildBlockDefinition();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($blockDefinition, $initialValues);

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
        $blockDefinition = $this->buildBlockDefinition();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($blockDefinition, $initialValues);

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

    private function buildBlockDefinition(): BlockDefinitionInterface
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

        return BlockDefinition::fromArray(['parameterDefinitions' => $parameterDefinitions]);
    }
}
