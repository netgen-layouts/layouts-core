<?php

namespace Netgen\BlockManager\Tests\Transfer\Input\Integration;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Diff;
use Diff_Renderer_Text_Unified;
use Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\BlockManager\Block\BlockDefinitionFactory;
use Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Block\Registry\HandlerPluginRegistry;
use Netgen\BlockManager\Config\ConfigDefinitionFactory;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver;
use Netgen\BlockManager\Item\Item as CmsItem;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory;
use Netgen\BlockManager\Standard\Block\BlockDefinition\Handler\Container\ColumnHandler;
use Netgen\BlockManager\Standard\Block\BlockDefinition\Handler\Container\TwoColumnsHandler;
use Netgen\BlockManager\Standard\Block\BlockDefinition\Handler\ListHandler;
use Netgen\BlockManager\Standard\Block\BlockDefinition\Handler\TextHandler;
use Netgen\BlockManager\Standard\Block\BlockDefinition\Handler\TitleHandler;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler;
use Netgen\BlockManager\Transfer\Input\Importer;
use Netgen\BlockManager\Transfer\Output\Serializer;
use Netgen\BlockManager\Transfer\Output\Visitor;

abstract class ImporterTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\ImporterInterface
     */
    private $importer;

    /**
     * @var \Netgen\BlockManager\Transfer\Output\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Coduo\PHPMatcher\Factory\SimpleFactory
     */
    private $matcherFactory;

    public function setUp()
    {
        parent::setUp();

        $this->prepareBlockDefinitionRegistry();

        $this->blockService = $this->createBlockService();
        $this->collectionService = $this->createCollectionService();
        $this->layoutService = $this->createLayoutService();

        $this->itemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->will(
                $this->returnCallback(
                    function ($remoteId) {
                        return new CmsItem(
                            [
                                'value' => $remoteId,
                                'remoteId' => $remoteId,
                            ]
                        );
                    }
                )
            );

        $this->itemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->will(
                $this->returnCallback(
                    function ($value) {
                        return new CmsItem(
                            [
                                'value' => $value,
                                'remoteId' => $value,
                            ]
                        );
                    }
                )
            );

        $this->importer = new Importer(
            new LayoutDataHandler(
                $this->blockService,
                $this->collectionService,
                $this->layoutService,
                $this->blockDefinitionRegistry,
                $this->layoutTypeRegistry,
                $this->itemDefinitionRegistry,
                $this->queryTypeRegistry,
                $this->itemLoaderMock
            )
        );

        $this->serializer = new Serializer(
            new Visitor\Aggregate(
                [
                    new Visitor\Block($this->blockService),
                    new Visitor\Collection(),
                    new Visitor\Config(),
                    new Visitor\Item(),
                    new Visitor\Layout(),
                    new Visitor\Parameter(),
                    new Visitor\Placeholder(),
                    new Visitor\Query($this->collectionService),
                    new Visitor\Zone($this->blockService),
                ]
            )
        );

        $this->matcherFactory = new SimpleFactory();
    }

    /**
     * @param array $layoutData
     *
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::acceptLayout
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importLayout
     *
     * @dataProvider importLayoutProvider
     */
    public function testImportLayout(array $layoutData)
    {
        $importedLayout = $this->importer->importLayout($layoutData);

        $exportedLayoutData = $this->serializer->serializeLayout(
            $this->layoutService->loadLayout(
                $importedLayout->getId()
            )
        );

        // After we check that layout names are different, we remove them
        // from the data, so they don't kill the test
        $this->assertNotEquals($layoutData['name'], $exportedLayoutData['name']);
        unset($layoutData['name'], $exportedLayoutData['name']);

        $matcher = $this->matcherFactory->createMatcher();
        $matchResult = $matcher->match($exportedLayoutData, $layoutData);

        if (!$matchResult) {
            $prettyLayoutData = json_encode($layoutData, JSON_PRETTY_PRINT);
            $prettyExportedLayoutData = json_encode($exportedLayoutData, JSON_PRETTY_PRINT);
            $diff = new Diff(explode(PHP_EOL, $prettyExportedLayoutData), explode(PHP_EOL, $prettyLayoutData));

            $this->fail($matcher->getError() . PHP_EOL . $diff->render(new Diff_Renderer_Text_Unified()));
        }

        // Fake assertion to disable risky flag
        $this->assertTrue(true);
    }

    public function importLayoutProvider()
    {
        $layoutsData = json_decode(
            file_get_contents(
                __DIR__ . '/../../_fixtures/input/layouts.json'
            ),
            true
        );

        foreach ($layoutsData as $layoutData) {
            yield [$layoutData];
        }
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::acceptLayout
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importLayout
     *
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Unknown item type 'INVALID'
     */
    public function testImportLayoutWithInvalidItemTypeThrowsRuntimeException()
    {
        $layoutData = json_decode(
            file_get_contents(
                __DIR__ . '/../../_fixtures/input/invalid/layout_with_invalid_item_type.json'
            ),
            true
        )[0];

        $this->importer->importLayout($layoutData);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::acceptLayout
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importLayout
     *
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Could not find locale 'hr' in the given query data
     */
    public function testImportLayoutWithMissingQueryTranslationThrowsRuntimeException()
    {
        $layoutData = json_decode(
            file_get_contents(
                __DIR__ . '/../../_fixtures/input/invalid/layout_with_missing_query_translation.json'
            ),
            true
        )[0];

        $this->importer->importLayout($layoutData);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::acceptLayout
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importLayout
     *
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Could not find locale 'hr' in the given block data
     */
    public function testImportLayoutWithMissingBlockTranslationThrowsRuntimeException()
    {
        $layoutData = json_decode(
            file_get_contents(
                __DIR__ . '/../../_fixtures/input/invalid/layout_with_missing_block_translation.json'
            ),
            true
        )[0];

        $this->importer->importLayout($layoutData);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::acceptLayout
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importLayout
     *
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Missing data for zone 'right'
     */
    public function testImportLayoutWithMissingZoneThrowsRuntimeException()
    {
        $layoutData = json_decode(
            file_get_contents(
                __DIR__ . '/../../_fixtures/input/invalid/layout_with_missing_zone.json'
            ),
            true
        )[0];

        $this->importer->importLayout($layoutData);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::__construct
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::acceptLayout
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importLayout
     *
     * @expectedException \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException
     * @expectedExceptionMessage Could not find format information in the provided data.
     */
    public function testImportLayoutWithoutFormatThrowsDataNotAcceptedException()
    {
        $this->importer->importLayout([]);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::acceptLayout
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importLayout
     *
     * @expectedException \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException
     * @expectedExceptionMessage Supported type is "layout", type "invalid" was given.
     */
    public function testImportLayoutWithInvalidTypeThrowsDataNotAcceptedException()
    {
        $this->importer->importLayout(['__format' => ['type' => 'invalid', 'version' => 1]]);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::acceptLayout
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importLayout
     *
     * @expectedException \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException
     * @expectedExceptionMessage Supported type is "layout", type "" was given.
     */
    public function testImportLayoutWithMissingTypeThrowsDataNotAcceptedException()
    {
        $this->importer->importLayout(['__format' => ['version' => 1]]);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::acceptLayout
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importLayout
     *
     * @expectedException \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException
     * @expectedExceptionMessage Supported version is "1", version "9999" was given.
     */
    public function testImportLayoutWithInvalidVersionThrowsDataNotAcceptedException()
    {
        $this->importer->importLayout(['__format' => ['type' => 'layout', 'version' => 9999]]);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::acceptLayout
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importLayout
     *
     * @expectedException \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException
     * @expectedExceptionMessage Supported version is "1", version "" was given.
     */
    public function testImportLayoutWithMissingVersionThrowsDataNotAcceptedException()
    {
        $this->importer->importLayout(['__format' => ['type' => 'layout']]);
    }

    private function prepareBlockDefinitionRegistry()
    {
        $data = ['translatable' => true, 'view_types' => ['view_type' => ['enabled' => true]]];
        $configHandlers = ['http_cache' => new HttpCacheConfigHandler()];

        $handlerPluginRegistry = new HandlerPluginRegistry();
        $handlerPluginRegistry->addPlugin(new PagedCollectionsPlugin(['pager' => 'pager', 'load_more' => 'load_more']));
        $handlerPluginRegistry->addPlugin(new CommonParametersPlugin());

        $blockDefinitionFactory = new BlockDefinitionFactory(
            new TranslatableParameterBuilderFactory(
                $this->parameterTypeRegistry
            ),
            $handlerPluginRegistry,
            new ConfigDefinitionFactory(
                new ParameterBuilderFactory(
                    $this->parameterTypeRegistry
                )
            ),
            new CacheableResolver()
        );

        $this->blockDefinitionRegistry = new BlockDefinitionRegistry();

        $this->blockDefinitionRegistry->addBlockDefinition(
            'title',
            $blockDefinitionFactory->buildBlockDefinition(
                'title',
                new TitleHandler(['h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3']),
                $data,
                $configHandlers
            )
        );

        $this->blockDefinitionRegistry->addBlockDefinition(
            'text',
            $blockDefinitionFactory->buildBlockDefinition(
                'text',
                new TextHandler(),
                $data,
                $configHandlers
            )
        );

        $this->blockDefinitionRegistry->addBlockDefinition(
            'list',
            $blockDefinitionFactory->buildBlockDefinition(
                'list',
                new ListHandler([2 => '2', 3 => '3', 4 => '4', 6 => '6']),
                $data,
                $configHandlers
            )
        );

        $this->blockDefinitionRegistry->addBlockDefinition(
            'column',
            $blockDefinitionFactory->buildContainerDefinition(
                'column',
                new ColumnHandler(),
                $data,
                $configHandlers
            )
        );

        $this->blockDefinitionRegistry->addBlockDefinition(
            'two_columns',
            $blockDefinitionFactory->buildContainerDefinition(
                'two_columns',
                new TwoColumnsHandler(),
                $data,
                $configHandlers
            )
        );
    }
}
