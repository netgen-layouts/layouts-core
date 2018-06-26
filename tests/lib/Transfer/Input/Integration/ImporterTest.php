<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Input\Integration;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Diff;
use Diff_Renderer_Text_Unified;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\BlockManager\Block\BlockDefinitionFactory;
use Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Block\Registry\HandlerPluginRegistry;
use Netgen\BlockManager\Config\ConfigDefinitionFactory;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemInterface;
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
use Netgen\BlockManager\Transfer\Input\JsonValidator;
use Netgen\BlockManager\Transfer\Input\Result\ErrorResult;
use Netgen\BlockManager\Transfer\Input\Result\SuccessResult;
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

    public function setUp(): void
    {
        parent::setUp();

        $this->prepareBlockDefinitionRegistry();

        $this->blockService = $this->createBlockService();
        $this->collectionService = $this->createCollectionService();
        $this->layoutService = $this->createLayoutService();
        $this->layoutResolverService = $this->createLayoutResolverService();

        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->will(
                $this->returnCallback(
                    function ($remoteId): CmsItemInterface {
                        return new CmsItem(
                            [
                                'value' => $remoteId,
                                'remoteId' => $remoteId,
                            ]
                        );
                    }
                )
            );

        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->will(
                $this->returnCallback(
                    function ($value): CmsItemInterface {
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
            new JsonValidator(),
            new LayoutDataHandler(
                $this->blockService,
                $this->collectionService,
                $this->layoutService,
                $this->blockDefinitionRegistry,
                $this->layoutTypeRegistry,
                $this->itemDefinitionRegistry,
                $this->queryTypeRegistry,
                $this->cmsItemLoaderMock
            )
        );

        $this->serializer = new Serializer(
            $this->layoutService,
            $this->layoutResolverService,
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
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::__construct
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importData
     */
    public function testImportData(): void
    {
        $importData = (string) file_get_contents(__DIR__ . '/../../_fixtures/input/layouts.json');
        $decodedData = json_decode($importData, true);

        foreach ($this->importer->importData($importData) as $index => $result) {
            $this->assertInstanceOf(SuccessResult::class, $result);
            $this->assertInstanceOf(Layout::class, $result->getEntity());
            $this->assertSame($result->getEntity()->getId(), $result->getEntityId());

            $layoutData = $decodedData['entities'][$index];
            $exportedLayoutData = $this->serializer->serializeLayouts([$result->getEntityId()]);

            $exportedLayoutData = $exportedLayoutData['entities'][0];

            // After we check that layout names are different, we remove them
            // from the data, so they don't kill the test
            $this->assertNotEquals($layoutData['name'], $exportedLayoutData['name']);
            unset($layoutData['name'], $exportedLayoutData['name']);

            // Same goes for creation and modification date
            $this->assertGreaterThan($layoutData['creation_date'], $exportedLayoutData['creation_date']);
            unset($layoutData['creation_date'], $exportedLayoutData['creation_date']);

            $this->assertGreaterThan($layoutData['modification_date'], $exportedLayoutData['modification_date']);
            unset($layoutData['modification_date'], $exportedLayoutData['modification_date']);

            $matcher = $this->matcherFactory->createMatcher();
            $matchResult = $matcher->match($exportedLayoutData, $layoutData);

            if (!$matchResult) {
                $prettyLayoutData = (string) json_encode($layoutData, JSON_PRETTY_PRINT);
                $prettyExportedLayoutData = (string) json_encode($exportedLayoutData, JSON_PRETTY_PRINT);
                $diff = new Diff(explode(PHP_EOL, $prettyExportedLayoutData), explode(PHP_EOL, $prettyLayoutData));

                $this->fail($matcher->getError() . PHP_EOL . $diff->render(new Diff_Renderer_Text_Unified()));
            }
        }

        // Fake assertion to disable risky flag
        $this->assertTrue(true);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importData
     */
    public function testImportDataWithMissingQueryTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_query_parameters_in_translation.json'
        );

        $result = iterator_to_array($this->importer->importData($layoutData));

        $this->assertInstanceOf(ErrorResult::class, $result[0]);
        $this->assertInstanceOf(RuntimeException::class, $result[0]->getError());
        $this->assertSame('Could not find locale "hr" in the given query data', $result[0]->getError()->getMessage());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importData
     */
    public function testImportDataWithMissingMainQueryTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_query_parameters_in_main_translation.json'
        );

        $result = iterator_to_array($this->importer->importData($layoutData));

        $this->assertInstanceOf(ErrorResult::class, $result[0]);
        $this->assertInstanceOf(RuntimeException::class, $result[0]->getError());
        $this->assertSame('Missing data for query main locale "en"', $result[0]->getError()->getMessage());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importData
     */
    public function testImportDataWithMissingBlockTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_block_parameters_in_translation.json'
        );

        $result = iterator_to_array($this->importer->importData($layoutData));

        $this->assertInstanceOf(ErrorResult::class, $result[0]);
        $this->assertInstanceOf(RuntimeException::class, $result[0]->getError());
        $this->assertSame('Could not find locale "hr" in the given block data', $result[0]->getError()->getMessage());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importData
     */
    public function testImportDataWithMissingMainBlockTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_block_parameters_in_main_translation.json'
        );

        $result = iterator_to_array($this->importer->importData($layoutData));

        $this->assertInstanceOf(ErrorResult::class, $result[0]);
        $this->assertInstanceOf(RuntimeException::class, $result[0]->getError());
        $this->assertSame('Missing data for block main locale "en"', $result[0]->getError()->getMessage());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\BlockManager\Transfer\Input\Importer::importData
     */
    public function testImportDataWithMissingZoneThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_zone.json'
        );

        $result = iterator_to_array($this->importer->importData($layoutData));

        $this->assertInstanceOf(ErrorResult::class, $result[0]);
        $this->assertInstanceOf(RuntimeException::class, $result[0]->getError());
        $this->assertSame('Missing data for zone "right"', $result[0]->getError()->getMessage());
    }

    private function prepareBlockDefinitionRegistry(): void
    {
        $data = ['translatable' => true, 'view_types' => ['view_type' => ['enabled' => true]]];
        $configHandlers = ['http_cache' => new HttpCacheConfigHandler()];

        $handlerPluginRegistry = new HandlerPluginRegistry(
            [
                new PagedCollectionsPlugin(['pager' => 'pager', 'load_more' => 'load_more']),
                new CommonParametersPlugin(),
            ]
        );

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
