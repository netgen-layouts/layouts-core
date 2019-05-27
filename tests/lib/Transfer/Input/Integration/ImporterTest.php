<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input\Integration;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Diff;
use Diff_Renderer_Text_Unified;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Block\BlockDefinition\Handler\CommonParametersPlugin;
use Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\Layouts\Block\BlockDefinitionFactory;
use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Block\Registry\HandlerPluginRegistry;
use Netgen\Layouts\Config\ConfigDefinitionFactory;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\TranslatableParameterBuilderFactory;
use Netgen\Layouts\Standard\Block\BlockDefinition\Handler\Container\ColumnHandler;
use Netgen\Layouts\Standard\Block\BlockDefinition\Handler\Container\TwoColumnsHandler;
use Netgen\Layouts\Standard\Block\BlockDefinition\Handler\ListHandler;
use Netgen\Layouts\Standard\Block\BlockDefinition\Handler\TextHandler;
use Netgen\Layouts\Standard\Block\BlockDefinition\Handler\TitleHandler;
use Netgen\Layouts\Tests\Config\Stubs\Block\ConfigHandler;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler;
use Netgen\Layouts\Transfer\Input\Importer;
use Netgen\Layouts\Transfer\Input\JsonValidator;
use Netgen\Layouts\Transfer\Input\Result\ErrorResult;
use Netgen\Layouts\Transfer\Input\Result\SuccessResult;
use Netgen\Layouts\Transfer\Output\Serializer;
use Netgen\Layouts\Transfer\Output\Visitor;

abstract class ImporterTest extends CoreTestCase
{
    /**
     * @var \Netgen\Layouts\Transfer\Input\ImporterInterface
     */
    private $importer;

    /**
     * @var \Netgen\Layouts\Transfer\Output\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Coduo\PHPMatcher\Factory\SimpleFactory
     */
    private $matcherFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cmsItemLoaderMock
            ->expects(self::any())
            ->method('loadByRemoteId')
            ->willReturnCallback(
                static function ($remoteId): CmsItemInterface {
                    return CmsItem::fromArray(
                        [
                            'value' => $remoteId,
                            'remoteId' => $remoteId,
                        ]
                    );
                }
            );

        $this->cmsItemLoaderMock
            ->expects(self::any())
            ->method('load')
            ->willReturnCallback(
                static function ($value): CmsItemInterface {
                    return CmsItem::fromArray(
                        [
                            'value' => $value,
                            'remoteId' => $value,
                        ]
                    );
                }
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
            new Visitor\AggregateVisitor(
                [
                    new Visitor\BlockVisitor($this->blockService),
                    new Visitor\CollectionVisitor(),
                    new Visitor\ConfigVisitor(),
                    new Visitor\ItemVisitor(),
                    new Visitor\SlotVisitor(),
                    new Visitor\LayoutVisitor(),
                    new Visitor\ParameterVisitor(),
                    new Visitor\PlaceholderVisitor(),
                    new Visitor\QueryVisitor($this->collectionService),
                    new Visitor\ZoneVisitor($this->blockService),
                ]
            )
        );

        $this->matcherFactory = new SimpleFactory();
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\Layouts\Transfer\Input\Importer::__construct
     * @covers \Netgen\Layouts\Transfer\Input\Importer::importData
     */
    public function testImportData(): void
    {
        $importData = (string) file_get_contents(__DIR__ . '/../../_fixtures/input/layouts.json');
        $decodedData = json_decode((string) preg_replace('/[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/', '@uuid@', $importData), true);

        foreach ($this->importer->importData($importData) as $index => $result) {
            self::assertInstanceOf(SuccessResult::class, $result);
            self::assertInstanceOf(Layout::class, $result->getEntity());
            self::assertSame($result->getEntity()->getId()->toString(), $result->getEntityId()->toString());

            $layoutData = $decodedData['entities'][$index];
            $exportedLayoutData = $this->serializer->serializeLayouts([$result->getEntityId()->toString()]);

            $exportedLayoutData = $exportedLayoutData['entities'][0];

            // After we check that layout names are different, we remove them
            // from the data, so they don't kill the test
            self::assertNotSame($layoutData['name'], $exportedLayoutData['name']);
            unset($layoutData['name'], $exportedLayoutData['name']);

            // Same goes for creation and modification date
            self::assertGreaterThan($layoutData['creation_date'], $exportedLayoutData['creation_date']);
            unset($layoutData['creation_date'], $exportedLayoutData['creation_date']);

            self::assertGreaterThan($layoutData['modification_date'], $exportedLayoutData['modification_date']);
            unset($layoutData['modification_date'], $exportedLayoutData['modification_date']);

            $matcher = $this->matcherFactory->createMatcher();
            $matchResult = $matcher->match($exportedLayoutData, $layoutData);

            if (!$matchResult) {
                $prettyLayoutData = json_encode($layoutData, JSON_PRETTY_PRINT);
                $prettyExportedLayoutData = json_encode($exportedLayoutData, JSON_PRETTY_PRINT);
                $diff = new Diff(
                    explode(PHP_EOL, is_string($prettyExportedLayoutData) ? $prettyExportedLayoutData : ''),
                    explode(PHP_EOL, is_string($prettyLayoutData) ? $prettyLayoutData : '')
                );

                self::fail($matcher->getError() . PHP_EOL . $diff->render(new Diff_Renderer_Text_Unified()));
            }
        }

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\Layouts\Transfer\Input\Importer::importData
     */
    public function testImportDataWithMissingQueryTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_query_parameters_in_translation.json'
        );

        $result = iterator_to_array($this->importer->importData($layoutData));

        self::assertInstanceOf(ErrorResult::class, $result[0]);
        self::assertInstanceOf(RuntimeException::class, $result[0]->getError());
        self::assertSame('Could not find locale "hr" in the given query data', $result[0]->getError()->getMessage());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\Layouts\Transfer\Input\Importer::importData
     */
    public function testImportDataWithMissingMainQueryTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_query_parameters_in_main_translation.json'
        );

        $result = iterator_to_array($this->importer->importData($layoutData));

        self::assertInstanceOf(ErrorResult::class, $result[0]);
        self::assertInstanceOf(RuntimeException::class, $result[0]->getError());
        self::assertSame('Missing data for query main locale "en"', $result[0]->getError()->getMessage());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\Layouts\Transfer\Input\Importer::importData
     */
    public function testImportDataWithMissingBlockTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_block_parameters_in_translation.json'
        );

        $result = iterator_to_array($this->importer->importData($layoutData));

        self::assertInstanceOf(ErrorResult::class, $result[0]);
        self::assertInstanceOf(RuntimeException::class, $result[0]->getError());
        self::assertSame('Could not find locale "hr" in the given block data', $result[0]->getError()->getMessage());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\Layouts\Transfer\Input\Importer::importData
     */
    public function testImportDataWithMissingMainBlockTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_block_parameters_in_main_translation.json'
        );

        $result = iterator_to_array($this->importer->importData($layoutData));

        self::assertInstanceOf(ErrorResult::class, $result[0]);
        self::assertInstanceOf(RuntimeException::class, $result[0]->getError());
        self::assertSame('Missing data for block main locale "en"', $result[0]->getError()->getMessage());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler
     * @covers \Netgen\Layouts\Transfer\Input\Importer::importData
     */
    public function testImportDataWithMissingZoneThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_zone.json'
        );

        $result = iterator_to_array($this->importer->importData($layoutData));

        self::assertInstanceOf(ErrorResult::class, $result[0]);
        self::assertInstanceOf(RuntimeException::class, $result[0]->getError());
        self::assertSame('Missing data for zone "right"', $result[0]->getError()->getMessage());
    }

    protected function createBlockDefinitionRegistry(): BlockDefinitionRegistry
    {
        $data = ['translatable' => true, 'view_types' => ['view_type' => ['enabled' => true]]];
        $configHandlers = ['key' => new ConfigHandler()];

        $handlerPluginRegistry = new HandlerPluginRegistry(
            [
                new PagedCollectionsPlugin(['pager' => 'pager', 'load_more' => 'load_more'], []),
                new CommonParametersPlugin([]),
            ]
        );

        $blockDefinitionFactory = new BlockDefinitionFactory(
            new TranslatableParameterBuilderFactory(
                $this->createParameterTypeRegistry()
            ),
            $handlerPluginRegistry,
            new ConfigDefinitionFactory(
                new ParameterBuilderFactory(
                    $this->createParameterTypeRegistry()
                )
            )
        );

        $blockDefinition1 = $blockDefinitionFactory->buildBlockDefinition(
            'title',
            new TitleHandler(['h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3'], []),
            $data,
            $configHandlers
        );

        $blockDefinition2 = $blockDefinitionFactory->buildBlockDefinition(
            'text',
            new TextHandler(),
            $data,
            $configHandlers
        );

        $blockDefinition3 = $blockDefinitionFactory->buildBlockDefinition(
            'list',
            new ListHandler([2 => '2', 3 => '3', 4 => '4', 6 => '6']),
            $data,
            $configHandlers
        );

        $blockDefinition4 = $blockDefinitionFactory->buildContainerDefinition(
            'column',
            new ColumnHandler(),
            $data,
            $configHandlers
        );

        $blockDefinition5 = $blockDefinitionFactory->buildContainerDefinition(
            'two_columns',
            new TwoColumnsHandler(),
            $data,
            $configHandlers
        );

        return new BlockDefinitionRegistry(
            [
                'title' => $blockDefinition1,
                'text' => $blockDefinition2,
                'list' => $blockDefinition3,
                'column' => $blockDefinition4,
                'two_columns' => $blockDefinition5,
            ]
        );
    }
}
