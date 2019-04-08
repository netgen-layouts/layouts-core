<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\TestCase;

final class CommonParametersPluginTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin
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
        $this->plugin = new CommonParametersPlugin(['group']);

        $this->parameterTypeRegistry = new ParameterTypeRegistry(
            [
                new ParameterType\TextLineType(),
                new ParameterType\BooleanType(),
            ]
        );

        $this->parameterBuilderFactory = new TranslatableParameterBuilderFactory(
            $this->parameterTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin::getExtendedHandlers
     */
    public function testGetExtendedHandlers(): void
    {
        $plugin = $this->plugin;

        self::assertSame([BlockDefinitionHandlerInterface::class], $plugin::getExtendedHandlers());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin::buildParameters
     */
    public function testBuildParameters(): void
    {
        $builder = $this->parameterBuilderFactory->createParameterBuilder();
        $this->plugin->buildParameters($builder);

        self::assertCount(3, $builder);

        self::assertTrue($builder->has('css_class'));
        self::assertInstanceOf(ParameterType\TextLineType::class, $builder->get('css_class')->getType());
        self::assertSame(['group'], $builder->get('css_class')->getGroups());
        self::assertFalse($builder->get('css_class')->getOption('translatable'));

        self::assertTrue($builder->has('css_id'));
        self::assertInstanceOf(ParameterType\TextLineType::class, $builder->get('css_id')->getType());
        self::assertSame(['group'], $builder->get('css_id')->getGroups());
        self::assertFalse($builder->get('css_id')->getOption('translatable'));

        self::assertTrue($builder->has('set_container'));
        self::assertInstanceOf(ParameterType\BooleanType::class, $builder->get('set_container')->getType());
        self::assertSame(['group'], $builder->get('set_container')->getGroups());
        self::assertFalse($builder->get('set_container')->getOption('translatable'));
    }
}
