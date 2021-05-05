<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Handler;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Handler\Plugin;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PluginTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\Block\BlockDefinition\Handler\Plugin
     */
    private MockObject $plugin;

    private ParameterTypeRegistry $parameterTypeRegistry;

    private ParameterBuilderFactory $parameterBuilderFactory;

    protected function setUp(): void
    {
        $this->plugin = $this->getMockForAbstractClass(Plugin::class);

        $this->parameterTypeRegistry = new ParameterTypeRegistry(
            [
                new ParameterType\TextLineType(),
                new ParameterType\BooleanType(),
            ],
        );

        $this->parameterBuilderFactory = new ParameterBuilderFactory(
            $this->parameterTypeRegistry,
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Handler\Plugin::buildParameters
     */
    public function testBuildParameters(): void
    {
        $builder = $this->parameterBuilderFactory->createParameterBuilder();
        $this->plugin->buildParameters($builder);

        self::assertCount(0, $builder);
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Handler\Plugin::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = new DynamicParameters();
        $this->plugin->getDynamicParameters($dynamicParameters, new Block());

        self::assertCount(0, $dynamicParameters);
    }
}
