<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder;

use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Core\StructBuilder\ConfigStructBuilder;
use Netgen\Layouts\Tests\API\Stubs\ConfigAwareValue;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\Layouts\Tests\Core\CoreTestCase;

abstract class ConfigStructBuilderTestBase extends CoreTestCase
{
    private ConfigStructBuilder $structBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new ConfigStructBuilder();
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\ConfigStructBuilder::buildConfigUpdateStructs
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
                                ],
                            ),
                        ],
                    ),
                ],
            ],
        );

        $struct = new BlockUpdateStruct();

        $this->structBuilder->buildConfigUpdateStructs($block, $struct);

        self::assertArrayHasKey('config', $struct->getConfigStructs());

        $configStruct = $struct->getConfigStruct('config');

        self::assertSame(['param' => null, 'param2' => null], $configStruct->getParameterValues());
    }
}
