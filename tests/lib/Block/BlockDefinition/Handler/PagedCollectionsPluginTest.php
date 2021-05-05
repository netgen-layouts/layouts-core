<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Handler;

use Generator;
use Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsBlockInterface;
use Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\TestCase;

final class PagedCollectionsPluginTest extends TestCase
{
    private PagedCollectionsPlugin $plugin;

    private ParameterTypeRegistry $parameterTypeRegistry;

    private TranslatableParameterBuilderFactory $parameterBuilderFactory;

    protected function setUp(): void
    {
        $this->plugin = new PagedCollectionsPlugin(['load_more' => 'Load more'], ['group']);

        $this->parameterTypeRegistry = new ParameterTypeRegistry(
            [
                new ParameterType\ChoiceType(),
                new ParameterType\IntegerType(),
                new ParameterType\BooleanType(),
                new ParameterType\Compound\BooleanType(),
            ],
        );

        $this->parameterBuilderFactory = new TranslatableParameterBuilderFactory(
            $this->parameterTypeRegistry,
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsPlugin::__construct
     * @covers \Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsPlugin::getExtendedHandlers
     */
    public function testGetExtendedHandlers(): void
    {
        self::assertSame(
            [PagedCollectionsBlockInterface::class],
            [...(function (): Generator { yield from $this->plugin::getExtendedHandlers(); })()],
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsPlugin::buildParameters
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
