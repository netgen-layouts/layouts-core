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

    public function setUp()
    {
        $this->plugin = new PagedCollectionsPlugin(['load_more' => 'Load more'], ['group']);

        $this->parameterTypeRegistry = new ParameterTypeRegistry();
        $this->parameterTypeRegistry->addParameterType(new ParameterType\ChoiceType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IntegerType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\BooleanType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\Compound\BooleanType());

        $this->parameterBuilderFactory = new TranslatableParameterBuilderFactory(
            $this->parameterTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin::getExtendedHandler
     */
    public function testGetExtendedHandler()
    {
        $plugin = $this->plugin;

        $this->assertEquals(PagedCollectionsBlockInterface::class, $plugin::getExtendedHandler());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin::buildParameters
     */
    public function testBuildParameters()
    {
        $builder = $this->parameterBuilderFactory->createParameterBuilder();
        $this->plugin->buildParameters($builder);

        $this->assertCount(1, $builder);

        $this->assertTrue($builder->has('paged_collections:enabled'));
        $this->assertInstanceOf(ParameterType\Compound\BooleanType::class, $builder->get('paged_collections:enabled')->getType());
        $this->assertEquals(['group'], $builder->get('paged_collections:enabled')->getGroups());
        $this->assertTrue($builder->get('paged_collections:enabled')->getOption('translatable'));

        $compoundBuilder = $builder->get('paged_collections:enabled');

        $this->assertCount(3, $compoundBuilder);

        $this->assertTrue($compoundBuilder->has('paged_collections:type'));
        $this->assertInstanceOf(ParameterType\ChoiceType::class, $compoundBuilder->get('paged_collections:type')->getType());
        $this->assertEquals(['group'], $compoundBuilder->get('paged_collections:type')->getGroups());
        $this->assertEquals(['Load more' => 'load_more'], $compoundBuilder->get('paged_collections:type')->getOption('options'));
        $this->assertTrue($compoundBuilder->get('paged_collections:type')->getOption('translatable'));

        $this->assertTrue($compoundBuilder->has('paged_collections:max_pages'));
        $this->assertInstanceOf(ParameterType\IntegerType::class, $compoundBuilder->get('paged_collections:max_pages')->getType());
        $this->assertEquals(['group'], $compoundBuilder->get('paged_collections:max_pages')->getGroups());
        $this->assertEquals(1, $compoundBuilder->get('paged_collections:max_pages')->getOption('min'));
        $this->assertTrue($compoundBuilder->get('paged_collections:max_pages')->getOption('translatable'));

        $this->assertTrue($compoundBuilder->has('paged_collections:ajax_first'));
        $this->assertInstanceOf(ParameterType\BooleanType::class, $compoundBuilder->get('paged_collections:ajax_first')->getType());
        $this->assertEquals(['group'], $compoundBuilder->get('paged_collections:ajax_first')->getGroups());
        $this->assertTrue($compoundBuilder->get('paged_collections:ajax_first')->getOption('translatable'));
    }
}
