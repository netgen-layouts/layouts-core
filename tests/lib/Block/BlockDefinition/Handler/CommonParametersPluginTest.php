<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Handler;

use Generator;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\Handler\CommonParametersPlugin;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\TestCase;

final class CommonParametersPluginTest extends TestCase
{
    private CommonParametersPlugin $plugin;

    private ParameterTypeRegistry $parameterTypeRegistry;

    private TranslatableParameterBuilderFactory $parameterBuilderFactory;

    protected function setUp(): void
    {
        $this->plugin = new CommonParametersPlugin(['group']);

        $this->parameterTypeRegistry = new ParameterTypeRegistry(
            [
                new ParameterType\TextLineType(),
                new ParameterType\BooleanType(),
            ],
        );

        $this->parameterBuilderFactory = new TranslatableParameterBuilderFactory(
            $this->parameterTypeRegistry,
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Handler\CommonParametersPlugin::__construct
     * @covers \Netgen\Layouts\Block\BlockDefinition\Handler\CommonParametersPlugin::getExtendedHandlers
     */
    public function testGetExtendedHandlers(): void
    {
        self::assertSame(
            [BlockDefinitionHandlerInterface::class],
            [...(function (): Generator { yield from $this->plugin::getExtendedHandlers(); })()],
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Handler\CommonParametersPlugin::buildParameters
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
