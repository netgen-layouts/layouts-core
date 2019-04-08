<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsBlockInterface;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\TestCase;

final class PagedCollectionsPluginTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin
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
        $this->plugin = new PagedCollectionsPlugin(['load_more' => 'Load more'], ['group']);

        $this->parameterTypeRegistry = new ParameterTypeRegistry(
            [
                new ParameterType\ChoiceType(),
                new ParameterType\IntegerType(),
                new ParameterType\BooleanType(),
                new ParameterType\Compound\BooleanType(),
            ]
        );

        $this->parameterBuilderFactory = new TranslatableParameterBuilderFactory(
            $this->parameterTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin::getExtendedHandlers
     */
    public function testGetExtendedHandlers(): void
    {
        $plugin = $this->plugin;

        self::assertSame([PagedCollectionsBlockInterface::class], $plugin::getExtendedHandlers());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin::buildParameters
     */
    public function testBuildParameters(): void
    {
        $builder = $this->parameterBuilderFactory->createParameterBuilder();
        $this->plugin->buildParameters($builder);

        self::assertCount(1, $builder);

        self::assertTrue($builder->has('paged_collections:enabled'));
        self::assertInstanceOf(ParameterType\Compound\BooleanType::class, $builder->get('paged_collections:enabled')->getType());
        self::assertSame(['group'], $builder->get('paged_collections:enabled')->getGroups());
        self::assertTrue($builder->get('paged_collections:enabled')->getOption('translatable'));

        $compoundBuilder = $builder->get('paged_collections:enabled');

        self::assertCount(3, $compoundBuilder);

        self::assertTrue($compoundBuilder->has('paged_collections:type'));
        self::assertInstanceOf(ParameterType\ChoiceType::class, $compoundBuilder->get('paged_collections:type')->getType());
        self::assertSame(['group'], $compoundBuilder->get('paged_collections:type')->getGroups());
        self::assertSame(['Load more' => 'load_more'], $compoundBuilder->get('paged_collections:type')->getOption('options'));
        self::assertTrue($compoundBuilder->get('paged_collections:type')->getOption('translatable'));

        self::assertTrue($compoundBuilder->has('paged_collections:max_pages'));
        self::assertInstanceOf(ParameterType\IntegerType::class, $compoundBuilder->get('paged_collections:max_pages')->getType());
        self::assertSame(['group'], $compoundBuilder->get('paged_collections:max_pages')->getGroups());
        self::assertSame(1, $compoundBuilder->get('paged_collections:max_pages')->getOption('min'));
        self::assertTrue($compoundBuilder->get('paged_collections:max_pages')->getOption('translatable'));

        self::assertTrue($compoundBuilder->has('paged_collections:ajax_first'));
        self::assertInstanceOf(ParameterType\BooleanType::class, $compoundBuilder->get('paged_collections:ajax_first')->getType());
        self::assertSame(['group'], $compoundBuilder->get('paged_collections:ajax_first')->getGroups());
        self::assertTrue($compoundBuilder->get('paged_collections:ajax_first')->getOption('translatable'));
    }
}
