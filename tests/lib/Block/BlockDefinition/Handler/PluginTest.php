<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Handler\Plugin;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\TestCase;

final class PluginTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\Plugin
     */
    private $plugin;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    private $parameterTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    private $parameterBuilderFactory;

    public function setUp(): void
    {
        $this->plugin = $this->getMockForAbstractClass(Plugin::class);

        $this->parameterTypeRegistry = new ParameterTypeRegistry(
            [
                new ParameterType\TextLineType(),
                new ParameterType\BooleanType(),
            ]
        );

        $this->parameterBuilderFactory = new ParameterBuilderFactory(
            $this->parameterTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\Plugin::buildParameters
     */
    public function testBuildParameters(): void
    {
        $builder = $this->parameterBuilderFactory->createParameterBuilder();
        $this->plugin->buildParameters($builder);

        self::assertCount(0, $builder);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\Plugin::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = new DynamicParameters();
        $this->plugin->getDynamicParameters($dynamicParameters, new Block());

        self::assertCount(0, $dynamicParameters);
    }
}
