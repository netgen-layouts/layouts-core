<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareValue;

abstract class ConfigStructBuilderTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder
     */
    private $structBuilder;

    public function setUp()
    {
        parent::setUp();

        $this->structBuilder = new ConfigStructBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder::buildConfigUpdateStructs
     */
    public function testBuildConfigUpdateStructs()
    {
        $handler = new ConfigDefinitionHandler();

        $block = new ConfigAwareValue(
            [
                'configs' => [
                    'config' => new Config(
                        [
                            'definition' => new ConfigDefinition(
                                [
                                    'parameterDefinitions' => $handler->getParameterDefinitions(),
                                ]
                            ),
                        ]
                    ),
                ],
            ]
        );

        $struct = new BlockUpdateStruct();

        $this->structBuilder->buildConfigUpdateStructs($block, $struct);

        $this->assertEquals(
            [
                'config' => new ConfigStruct(
                    [
                        'parameterValues' => [
                            'param' => null,
                            'param2' => null,
                        ],
                    ]
                ),
            ],
            $struct->getConfigStructs()
        );
    }
}
