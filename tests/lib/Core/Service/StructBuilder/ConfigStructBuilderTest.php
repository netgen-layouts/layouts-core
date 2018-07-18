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

    public function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new ConfigStructBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder::buildConfigUpdateStructs
     */
    public function testBuildConfigUpdateStructs(): void
    {
        $handler = new ConfigDefinitionHandler();

        $block = ConfigAwareValue::fromArray(
            [
                'configs' => [
                    'config' => Config::fromArray(
                        [
                            'definition' => ConfigDefinition::fromArray(
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

        $this->assertArrayHasKey('config', $struct->getConfigStructs());

        $configStruct = $struct->getConfigStructs()['config'];

        $this->assertInstanceOf(ConfigStruct::class, $configStruct);
        $this->assertSame(['param' => null, 'param2' => null], $configStruct->getParameterValues());
    }
}
