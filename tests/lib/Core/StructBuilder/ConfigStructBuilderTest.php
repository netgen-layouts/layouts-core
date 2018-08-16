<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\StructBuilder;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Core\StructBuilder\ConfigStructBuilder;
use Netgen\BlockManager\Tests\API\Stubs\ConfigAwareValue;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\Core\CoreTestCase;

abstract class ConfigStructBuilderTest extends CoreTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\StructBuilder\ConfigStructBuilder
     */
    private $structBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new ConfigStructBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Core\StructBuilder\ConfigStructBuilder::buildConfigUpdateStructs
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

        self::assertArrayHasKey('config', $struct->getConfigStructs());

        $configStruct = $struct->getConfigStruct('config');

        self::assertSame(['param' => null, 'param2' => null], $configStruct->getParameterValues());
    }
}
