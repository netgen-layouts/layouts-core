<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input\Integration;

use Coduo\PHPMatcher\PHPMatcher;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Transfer\EntityHandler\LayoutEntityHandler;
use Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler;
use Netgen\Layouts\Transfer\EntityHandler\RuleGroupEntityHandler;
use Netgen\Layouts\Transfer\Input\Importer;
use Netgen\Layouts\Transfer\Input\ImportOptions;
use Netgen\Layouts\Transfer\Input\JsonValidator;
use Netgen\Layouts\Transfer\Input\Result\ErrorResult;
use Netgen\Layouts\Transfer\Input\Result\SuccessResult;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\Serializer;
use Netgen\Layouts\Transfer\Output\Visitor;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

use function file_get_contents;
use function json_decode;
use function json_encode;
use function preg_replace;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

abstract class ImporterTestBase extends CoreTestCase
{
    private Importer $importer;

    private Serializer $serializer;

    final protected function setUp(): void
    {
        parent::setUp();

        $this->cmsItemLoaderStub
            ->method('loadByRemoteId')
            ->willReturnCallback(
                static fn (string $remoteId): CmsItemInterface => CmsItem::fromArray(
                    [
                        'value' => $remoteId,
                        'remoteId' => $remoteId,
                    ],
                ),
            );

        $this->cmsItemLoaderStub
            ->method('load')
            ->willReturnCallback(
                static fn (int|string $value): CmsItemInterface => CmsItem::fromArray(
                    [
                        'value' => $value,
                        'remoteId' => $value,
                    ],
                ),
            );

        $entityHandlers = [
            'layout' => new LayoutEntityHandler(
                $this->blockService,
                $this->collectionService,
                $this->layoutService,
                $this->blockDefinitionRegistry,
                $this->layoutTypeRegistry,
                $this->itemDefinitionRegistry,
                $this->queryTypeRegistry,
                $this->cmsItemLoaderStub,
            ),
            'rule' => new RuleEntityHandler(
                $this->layoutResolverService,
                $this->targetTypeRegistry,
                $this->conditionTypeRegistry,
            ),
            'rule_group' => new RuleGroupEntityHandler(
                $this->layoutResolverService,
                new RuleEntityHandler(
                    $this->layoutResolverService,
                    $this->targetTypeRegistry,
                    $this->conditionTypeRegistry,
                ),
                $this->conditionTypeRegistry,
            ),
        ];

        $this->importer = new Importer(
            $this->transactionService,
            new JsonValidator(),
            new Container($entityHandlers),
        );

        /** @var iterable<\Netgen\Layouts\Transfer\Output\VisitorInterface<object>> $outputVisitors */
        $outputVisitors = [
            new Visitor\BlockVisitor($this->blockService),
            new Visitor\CollectionVisitor(),
            new Visitor\ConfigVisitor(),
            new Visitor\ItemVisitor(),
            new Visitor\SlotVisitor(),
            new Visitor\LayoutVisitor(),
            new Visitor\PlaceholderVisitor(),
            new Visitor\QueryVisitor($this->collectionService),
            new Visitor\ZoneVisitor($this->blockService),
            new Visitor\RuleVisitor(),
            new Visitor\RuleGroupVisitor($this->layoutResolverService),
            new Visitor\TargetVisitor(),
            new Visitor\ConditionVisitor(),
        ];

        $this->serializer = new Serializer(
            new OutputVisitor($outputVisitors),
            new Container($entityHandlers),
        );
    }

    final public function testImportRules(): void
    {
        $importData = (string) file_get_contents(__DIR__ . '/../../_fixtures/input/rules.json');

        $decodedData = json_decode(
            (string) preg_replace(
                // @uuid@ matcher from coduo/php-matcher does not support NIL UUID
                '/(?!00000000-0000-0000-0000-000000000000)([0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12})/',
                '@uuid@',
                $importData,
            ),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        foreach ($this->importer->importData($importData, new ImportOptions()) as $index => $result) {
            self::assertInstanceOf(SuccessResult::class, $result);
            self::assertInstanceOf(Rule::class, $result->entity);
            self::assertSame($result->entity->id->toString(), $result->entityId->toString());

            $ruleData = $decodedData['rules'][$index];
            $exportedRuleData = $this->serializer->serialize([$result->entityId->toString() => $ruleData['__type']]);

            $exportedRuleData = $exportedRuleData['rules'][0];

            $matcher = new PHPMatcher();
            $matchResult = $matcher->match($exportedRuleData, $ruleData);

            if (!$matchResult) {
                $differ = new Differ(new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n", false));
                $diff = $differ->diff(
                    json_encode($ruleData, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                    json_encode($exportedRuleData, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                );

                self::fail($matcher->error() . PHP_EOL . $diff);
            }
        }

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    final public function testImportRuleGroups(): void
    {
        $importData = (string) file_get_contents(__DIR__ . '/../../_fixtures/input/rule_groups.json');

        $decodedData = json_decode(
            (string) preg_replace(
                // @uuid@ matcher from coduo/php-matcher does not support NIL UUID
                '/(?!00000000-0000-0000-0000-000000000000)([0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12})/',
                '@uuid@',
                $importData,
            ),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        foreach ($this->importer->importData($importData, new ImportOptions()) as $index => $result) {
            self::assertInstanceOf(SuccessResult::class, $result);
            self::assertInstanceOf(RuleGroup::class, $result->entity);
            self::assertSame($result->entity->id->toString(), $result->entityId->toString());

            $ruleGroupData = $decodedData['rule_groups'][$index];
            $exportedRuleGroupData = $this->serializer->serialize([$result->entityId->toString() => $ruleGroupData['__type']]);

            $exportedRuleGroupData = $exportedRuleGroupData['rule_groups'][0];

            $matcher = new PHPMatcher();
            $matchResult = $matcher->match($exportedRuleGroupData, $ruleGroupData);

            if (!$matchResult) {
                $differ = new Differ(new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n", false));
                $diff = $differ->diff(
                    json_encode($ruleGroupData, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                    json_encode($exportedRuleGroupData, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                );

                self::fail($matcher->error() . PHP_EOL . $diff);
            }
        }

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    final public function testImportLayouts(): void
    {
        $importData = (string) file_get_contents(__DIR__ . '/../../_fixtures/input/layouts.json');
        $decodedData = json_decode((string) preg_replace('/[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/', '@uuid@', $importData), true, 512, JSON_THROW_ON_ERROR);

        foreach ($this->importer->importData($importData, new ImportOptions()) as $index => $result) {
            self::assertInstanceOf(SuccessResult::class, $result);
            self::assertInstanceOf(Layout::class, $result->entity);
            self::assertSame($result->entity->id->toString(), $result->entityId->toString());

            $layoutData = $decodedData['layouts'][$index];
            $exportedLayoutData = $this->serializer->serialize([$result->entityId->toString() => $layoutData['__type']]);

            $exportedLayoutData = $exportedLayoutData['layouts'][0];

            // After we check that layout names are different, we remove them
            // from the data, so they don't kill the test
            self::assertNotSame($layoutData['name'], $exportedLayoutData['name']);
            unset($layoutData['name'], $exportedLayoutData['name']);

            // Same goes for creation and modification date
            self::assertGreaterThan($layoutData['creation_date'], $exportedLayoutData['creation_date']);
            unset($layoutData['creation_date'], $exportedLayoutData['creation_date']);

            self::assertGreaterThan($layoutData['modification_date'], $exportedLayoutData['modification_date']);
            unset($layoutData['modification_date'], $exportedLayoutData['modification_date']);

            $matcher = new PHPMatcher();
            $matchResult = $matcher->match($exportedLayoutData, $layoutData);

            if (!$matchResult) {
                $differ = new Differ(new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n", false));
                $diff = $differ->diff(
                    json_encode($layoutData, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                    json_encode($exportedLayoutData, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                );

                self::fail($matcher->error() . PHP_EOL . $diff);
            }
        }

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    final public function testImportLayoutsWithMissingQueryTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_query_parameters_in_translation.json',
        );

        $result = [...$this->importer->importData($layoutData, new ImportOptions())];

        self::assertInstanceOf(ErrorResult::class, $result[0]);
        self::assertInstanceOf(RuntimeException::class, $result[0]->error);
        self::assertSame('Could not find locale "hr" in the given query data', $result[0]->error->getMessage());
    }

    final public function testImportLayoutsWithMissingMainQueryTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_query_parameters_in_main_translation.json',
        );

        $result = [...$this->importer->importData($layoutData, new ImportOptions())];

        self::assertInstanceOf(ErrorResult::class, $result[0]);
        self::assertInstanceOf(RuntimeException::class, $result[0]->error);
        self::assertSame('Missing data for query main locale "en"', $result[0]->error->getMessage());
    }

    final public function testImportLayoutsWithMissingBlockTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_block_parameters_in_translation.json',
        );

        $result = [...$this->importer->importData($layoutData, new ImportOptions())];

        self::assertInstanceOf(ErrorResult::class, $result[0]);
        self::assertInstanceOf(RuntimeException::class, $result[0]->error);
        self::assertSame('Could not find locale "hr" in the given block data', $result[0]->error->getMessage());
    }

    final public function testImportLayoutsWithMissingMainBlockTranslationThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_block_parameters_in_main_translation.json',
        );

        $result = [...$this->importer->importData($layoutData, new ImportOptions())];

        self::assertInstanceOf(ErrorResult::class, $result[0]);
        self::assertInstanceOf(RuntimeException::class, $result[0]->error);
        self::assertSame('Missing data for block main locale "en"', $result[0]->error->getMessage());
    }

    final public function testImportLayoutsWithMissingZoneThrowsRuntimeException(): void
    {
        $layoutData = (string) file_get_contents(
            __DIR__ . '/../../_fixtures/input/invalid/missing_zone.json',
        );

        $result = [...$this->importer->importData($layoutData, new ImportOptions())];

        self::assertInstanceOf(ErrorResult::class, $result[0]);
        self::assertInstanceOf(RuntimeException::class, $result[0]->error);
        self::assertSame('Missing data for zone "right"', $result[0]->error->getMessage());
    }
}
