<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Tests\Core\CoreTestCase;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;

abstract class BlockServiceTest extends CoreTestCase
{
    use ExportObjectTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->blockService = $this->createBlockService();

        $this->layoutService = $this->createLayoutService();

        $this->collectionService = $this->createCollectionService();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     */
    public function testLoadBlock(): void
    {
        $block = $this->blockService->loadBlock(31);

        self::assertTrue($block->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "999999"
     */
    public function testLoadBlockThrowsNotFoundException(): void
    {
        $this->blockService->loadBlock(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "1"
     */
    public function testLoadBlockThrowsNotFoundExceptionOnLoadingInternalBlock(): void
    {
        $this->blockService->loadBlock(1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     */
    public function testLoadBlockDraft(): void
    {
        $block = $this->blockService->loadBlockDraft(31);

        self::assertTrue($block->isDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "999999"
     */
    public function testLoadBlockDraftThrowsNotFoundException(): void
    {
        $this->blockService->loadBlockDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "1"
     */
    public function testLoadBlockDraftThrowsNotFoundExceptionOnLoadingInternalBlock(): void
    {
        $this->blockService->loadBlockDraft(1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadZoneBlocks
     */
    public function testLoadZoneBlocks(): void
    {
        $blocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZone(1, 'right')
        );

        self::assertCount(2, $blocks);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadLayoutBlocks
     */
    public function testLoadLayoutBlocks(): void
    {
        $blocks = $this->blockService->loadLayoutBlocks(
            $this->layoutService->loadLayout(1)
        );

        self::assertCount(3, $blocks);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::hasPublishedState
     */
    public function testHasPublishedState(): void
    {
        $block = $this->blockService->loadBlock(31);

        self::assertTrue($this->blockService->hasPublishedState($block));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::hasPublishedState
     */
    public function testHasPublishedStateReturnsFalse(): void
    {
        $block = $this->blockService->loadBlockDraft(36);

        self::assertFalse($this->blockService->hasPublishedState($block));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlock(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $targetBlock = $this->blockService->loadBlockDraft(33);

        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0);

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $leftPlaceholder = $targetBlock->getPlaceholder('left');

        self::assertTrue($block->isDraft());
        self::assertSame($block->getId(), $leftPlaceholder->getBlocks()[0]->getId());

        self::assertSame(37, $leftPlaceholder->getBlocks()[1]->getId());

        self::assertFalse($block->isTranslatable());
        self::assertTrue($block->isAlwaysAvailable());
        self::assertContains('en', $block->getAvailableLocales());
        self::assertSame('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithCollection(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $blockCreateStruct->addCollectionCreateStruct('default', new CollectionCreateStruct());

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0);

        self::assertTrue($block->isDraft());

        $collections = $block->getCollections();
        self::assertCount(1, $collections);
        self::assertArrayHasKey('default', $collections);

        self::assertSame(0, $collections['default']->getOffset());
        self::assertNull($collections['default']->getLimit());

        $collection = $this->collectionService->loadCollectionDraft(7);
        self::assertFalse($collection->hasQuery());

        self::assertSame($block->isTranslatable(), $collection->isTranslatable());
        self::assertSame($block->isAlwaysAvailable(), $collection->isAlwaysAvailable());
        self::assertSame($block->getAvailableLocales(), $collection->getAvailableLocales());
        self::assertSame($block->getMainLocale(), $collection->getMainLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithDynamicCollection(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $blockCreateStruct->isTranslatable = true;

        $queryCreateStruct = new QueryCreateStruct(
            $this->queryTypeRegistry->getQueryType('my_query_type')
        );

        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->queryCreateStruct = $queryCreateStruct;

        $blockCreateStruct->addCollectionCreateStruct('default', $collectionCreateStruct);

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0);

        self::assertTrue($block->isDraft());

        $collections = $block->getCollections();
        self::assertCount(1, $collections);
        self::assertArrayHasKey('default', $collections);

        self::assertSame(0, $collections['default']->getOffset());
        self::assertNull($collections['default']->getLimit());

        $collection = $this->collectionService->loadCollectionDraft(7);
        self::assertTrue($collection->hasQuery());
        self::assertInstanceOf(Query::class, $collection->getQuery());
        self::assertSame('my_query_type', $collection->getQuery()->getQueryType()->getType());

        self::assertSame($block->isTranslatable(), $collection->isTranslatable());
        self::assertSame($block->isAlwaysAvailable(), $collection->isAlwaysAvailable());
        self::assertSame($block->getAvailableLocales(), $collection->getAvailableLocales());
        self::assertSame($block->getMainLocale(), $collection->getMainLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateTranslatableBlock(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $blockCreateStruct->isTranslatable = true;

        $zone = $this->layoutService->loadZoneDraft(1, 'left');

        $block = $this->blockService->createBlockInZone($blockCreateStruct, $zone, 0);

        self::assertTrue($block->isDraft());
        self::assertTrue($block->isTranslatable());
        self::assertSame('en', $block->getMainLocale());
        self::assertCount(2, $block->getAvailableLocales());
        self::assertContains('en', $block->getAvailableLocales());
        self::assertContains('hr', $block->getAvailableLocales());
        self::assertSame('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     */
    public function testCreateTranslatableBlockWithNonTranslatableTargetBlock(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $targetBlock = $this->blockService->disableTranslations(
            $this->blockService->loadBlockDraft(33)
        );

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $targetBlock,
            'left',
            0
        );

        self::assertTrue($block->isDraft());
        self::assertFalse($block->isTranslatable());
        self::assertSame('en', $block->getMainLocale());
        self::assertCount(1, $block->getAvailableLocales());
        self::assertContains('en', $block->getAvailableLocales());
        self::assertNotContains('hr', $block->getAvailableLocales());
        self::assertSame('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithConfig(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('param1', true);
        $configStruct->setParameterValue('param2', 400);

        $blockCreateStruct->setConfigStruct(
            'key',
            $configStruct
        );

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0);

        self::assertTrue($block->isDraft());
        self::assertTrue($block->hasConfig('key'));

        $blockConfig = $block->getConfig('key');
        self::assertTrue($blockConfig->getParameter('param1')->getValue());
        self::assertSame(400, $blockConfig->getParameter('param2')->getValue());

        self::assertFalse($block->isTranslatable());
        self::assertContains('en', $block->getAvailableLocales());
        self::assertSame('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Blocks can only be created in blocks in draft status.
     */
    public function testCreateBlockThrowsBadStateExceptionWithNonDraftTargetBlock(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlock(33),
            'left',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Target block is not a container.
     */
    public function testCreateBlockThrowsBadStateExceptionWithNonContainerTargetBlock(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(31),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.
     */
    public function testCreateBlockThrowsBadStateExceptionWithNoPlaceholder(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(33),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithNoPosition(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $targetBlock = $this->blockService->loadBlockDraft(33);

        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left');

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $leftPlaceholder = $targetBlock->getPlaceholder('left');

        self::assertTrue($block->isDraft());
        self::assertSame($block->getId(), $leftPlaceholder->getBlocks()[1]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCreateBlockThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(33),
            'left',
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "blockCreateStruct" has an invalid state. Containers cannot be placed inside containers.
     */
    public function testCreateBlockThrowsBadStateExceptionWithContainerInsideContainer(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('column')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(33),
            'left'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZone(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        $zone = $this->layoutService->loadZoneDraft(1, 'right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        self::assertTrue($block->isDraft());
        self::assertSame($block->getId(), $blocks[0]->getId());

        self::assertSame(31, $blocks[1]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithContainerBlock(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('column')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'left'),
            0
        );

        self::assertCount(2, $block->getPlaceholders());

        self::assertTrue($block->hasPlaceholder('main'));
        self::assertTrue($block->hasPlaceholder('other'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithoutCollection(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        $zone = $this->layoutService->loadZoneDraft(1, 'right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        self::assertTrue($block->isDraft());
        self::assertSame($block->getId(), $blocks[0]->getId());

        self::assertSame(31, $blocks[1]->getId());

        $collections = $block->getCollections();
        self::assertCount(0, $collections);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWhichDoesNotExistInLayoutType(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(7, 'center'),
            0
        );

        self::assertTrue($block->isDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Blocks can only be created in zones in draft status.
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZone(1, 'right'),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithNonExistentLayoutType(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(2, 'top')
        );

        self::assertTrue($block->isDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithNoPosition(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'right')
        );

        $zone = $this->layoutService->loadZoneDraft(1, 'right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        self::assertTrue($block->isDraft());
        self::assertSame($block->getId(), $blocks[2]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'right'),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Block is not allowed in specified zone.
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWithWithDisallowedIdentifier(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('gallery')
        );

        $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'right')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlock(): void
    {
        $block = $this->blockService->loadBlockDraft(31, ['en']);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('hr');
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $updatedBlock = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertTrue($updatedBlock->isDraft());
        self::assertSame('small', $updatedBlock->getViewType());
        self::assertSame('Super cool block', $updatedBlock->getName());

        self::assertSame('css-class', $updatedBlock->getParameter('css_class')->getValue());
        self::assertSame('css-id', $updatedBlock->getParameter('css_id')->getValue());

        $croBlock = $this->blockService->loadBlockDraft(31, ['hr']);

        self::assertSame('test_value', $croBlock->getParameter('css_class')->getValue());

        // CSS ID is untranslatable, meaning it keeps the value from main locale
        self::assertSame('css-id', $croBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockInMainLocale(): void
    {
        $block = $this->blockService->loadBlockDraft(31, ['en']);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $updatedBlock = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertTrue($updatedBlock->isDraft());
        self::assertSame('small', $updatedBlock->getViewType());
        self::assertSame('Super cool block', $updatedBlock->getName());

        self::assertSame('test_value', $updatedBlock->getParameter('css_class')->getValue());
        self::assertSame('some_other_test_value', $updatedBlock->getParameter('css_id')->getValue());

        $croBlock = $this->blockService->loadBlockDraft(31, ['hr']);

        self::assertSame('css-class-hr', $croBlock->getParameter('css_class')->getValue());

        // CSS ID is untranslatable, meaning it receives the value from the main locale
        self::assertSame('some_other_test_value', $croBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithUntranslatableParameters(): void
    {
        $block = $this->blockService->loadBlockDraft(31, ['en']);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');
        $blockUpdateStruct->setParameterValue('css_class', 'english_css');

        $this->blockService->updateBlock($block, $blockUpdateStruct);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('hr');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value_2');
        $blockUpdateStruct->setParameterValue('css_class', 'croatian_css');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $croBlock = $this->blockService->loadBlockDraft(31, ['hr']);

        self::assertSame('english_css', $block->getParameter('css_class')->getValue());
        self::assertSame('some_other_test_value', $block->getParameter('css_id')->getValue());

        self::assertSame('croatian_css', $croBlock->getParameter('css_class')->getValue());
        self::assertSame('some_other_test_value', $croBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithConfig(): void
    {
        $block = $this->blockService->loadBlockDraft(32);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('hr');

        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('param1', true);
        $configStruct->setParameterValue('param2', 400);

        $blockUpdateStruct->setConfigStruct('key', $configStruct);

        $updatedBlock = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertTrue($updatedBlock->isDraft());
        self::assertTrue($updatedBlock->hasConfig('key'));

        $blockConfig = $updatedBlock->getConfig('key');
        self::assertTrue($blockConfig->getParameter('param1')->getValue());
        self::assertSame(400, $blockConfig->getParameter('param2')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithBlankName(): void
    {
        $block = $this->blockService->loadBlockDraft(31);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $updatedBlock = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertTrue($updatedBlock->isDraft());
        self::assertSame('small', $updatedBlock->getViewType());
        self::assertSame('My block', $updatedBlock->getName());

        self::assertSame('test_value', $updatedBlock->getParameter('css_class')->getValue());
        self::assertSame('some_other_test_value', $updatedBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithBlankViewType(): void
    {
        $block = $this->blockService->loadBlockDraft(31);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $updatedBlock = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertTrue($updatedBlock->isDraft());
        self::assertSame('list', $updatedBlock->getViewType());
        self::assertSame('Super cool block', $updatedBlock->getName());

        self::assertSame('test_value', $updatedBlock->getParameter('css_class')->getValue());
        self::assertSame('some_other_test_value', $updatedBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be updated.
     */
    public function testUpdateBlockThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $block = $this->blockService->loadBlock(31);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $this->blockService->updateBlock($block, $blockUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Block does not have the specified translation.
     */
    public function testUpdateBlockThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $block = $this->blockService->loadBlockDraft(31);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('de');
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $this->blockService->updateBlock($block, $blockUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlock(): void
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'left'
        );

        $originalBlock = $this->blockService->loadBlockDraft(34);
        self::assertSame(0, $originalBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame(39, $copiedBlock->getId());
        self::assertSame(1, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockWithPosition(): void
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'left',
            1
        );

        $originalBlock = $this->blockService->loadBlockDraft(34);
        self::assertSame(0, $originalBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame(39, $copiedBlock->getId());
        self::assertSame(1, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockWithSamePosition(): void
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'left',
            0
        );

        $firstBlockInTargetBlock = $this->blockService->loadBlockDraft(37);
        self::assertSame(1, $firstBlockInTargetBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame(39, $copiedBlock->getId());
        self::assertSame(0, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCopyBlockThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'left',
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be copied.
     */
    public function testCopyBlockThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlock(34),
            $this->blockService->loadBlockDraft(33),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. You can only copy blocks to draft blocks.
     */
    public function testCopyBlockThrowsBadStateExceptionWithNonDraftTargetBlock(): void
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlock(33),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Target block is not a container.
     */
    public function testCopyBlockThrowsBadStateExceptionWithNonContainerTargetBlock(): void
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(37),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.
     */
    public function testCopyBlockThrowsBadStateExceptionWithNoPlaceholder(): void
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Containers cannot be placed inside containers.
     */
    public function testCopyBlockThrowsBadStateExceptionWithContainerInsideContainer(): void
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(33),
            $this->blockService->loadBlockDraft(38),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. You can only copy block to blocks in the same layout.
     */
    public function testCopyBlockThrowsBadStateExceptionWhenTargetBlockIsInDifferentLayout(): void
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(31),
            $this->blockService->loadBlockDraft(33),
            'left'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZone(): void
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'right')
        );

        $originalBlock = $this->blockService->loadBlockDraft(31);
        self::assertSame(0, $originalBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(35);
        self::assertSame(1, $secondBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame(39, $copiedBlock->getId());
        self::assertSame(2, $copiedBlock->getPosition());

        $copiedCollection = $this->collectionService->loadCollectionDraft(7);
        self::assertTrue($copiedCollection->isDraft());

        $copiedCollection2 = $this->collectionService->loadCollectionDraft(8);
        self::assertTrue($copiedCollection2->isDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithPosition(): void
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'right'),
            1
        );

        $originalBlock = $this->blockService->loadBlockDraft(31);
        self::assertSame(0, $originalBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(35);
        self::assertSame(2, $secondBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame(39, $copiedBlock->getId());
        self::assertSame(1, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithSamePosition(): void
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        $originalBlock = $this->blockService->loadBlockDraft(31);
        self::assertSame(1, $originalBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(35);
        self::assertSame(2, $secondBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame(39, $copiedBlock->getId());
        self::assertSame(0, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithLastPosition(): void
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'right'),
            2
        );

        $originalBlock = $this->blockService->loadBlockDraft(31);
        self::assertSame(0, $originalBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(35);
        self::assertSame(1, $secondBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame(39, $copiedBlock->getId());
        self::assertSame(2, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithLowerPosition(): void
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(35),
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        $originalBlock = $this->blockService->loadBlockDraft(31);
        self::assertSame(1, $originalBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(35);
        self::assertSame(2, $secondBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame(39, $copiedBlock->getId());
        self::assertSame(0, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'right'),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be copied.
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlock(31),
            $this->layoutService->loadZoneDraft(1, 'left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. You can only copy blocks in draft zones.
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZone(1, 'left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Block is not allowed in specified zone.
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWithDisallowedIdentifier(): void
    {
        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'bottom')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. You can only copy block to zone in the same layout.
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWhenZoneIsInDifferentLayout(): void
    {
        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(32),
            $this->layoutService->loadZoneDraft(4, 'bottom')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlock(): void
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'left',
            0
        );

        self::assertTrue($movedBlock->isDraft());
        self::assertSame(34, $movedBlock->getId());

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $leftPlaceholder = $targetBlock->getPlaceholder('left');

        self::assertSame($movedBlock->getId(), $leftPlaceholder->getBlocks()[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockToDifferentPlaceholder(): void
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(37),
            $this->blockService->loadBlockDraft(33),
            'right',
            0
        );

        self::assertTrue($movedBlock->isDraft());
        self::assertSame(37, $movedBlock->getId());

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $leftPlaceholder = $targetBlock->getPlaceholder('left');
        $rightPlaceholder = $targetBlock->getPlaceholder('right');

        self::assertEmpty($leftPlaceholder->getBlocks());
        self::assertSame($movedBlock->getId(), $rightPlaceholder->getBlocks()[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockToDifferentBlock(): void
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(37),
            $this->blockService->loadBlockDraft(38),
            'main',
            0
        );

        self::assertTrue($movedBlock->isDraft());
        self::assertSame(37, $movedBlock->getId());

        $originalBlock = $this->blockService->loadBlockDraft(33);
        $targetBlock = $this->blockService->loadBlockDraft(38);
        $originalPlaceholder = $originalBlock->getPlaceholder('left');
        $targetPlaceholder = $targetBlock->getPlaceholder('main');

        self::assertEmpty($originalPlaceholder->getBlocks());
        self::assertSame($movedBlock->getId(), $targetPlaceholder->getBlocks()[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be moved.
     */
    public function testMoveBlockThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlock(31),
            $this->blockService->loadBlockDraft(33),
            'left',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. You can only move blocks to draft blocks.
     */
    public function testMoveBlockThrowsBadStateExceptionWithNonDraftTargetBlock(): void
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(31),
            $this->blockService->loadBlock(33),
            'left',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Target block is not a container.
     */
    public function testMoveBlockThrowsBadStateExceptionWhenTargetBlockIsNotContainer(): void
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(32),
            $this->blockService->loadBlockDraft(31),
            'left',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.
     */
    public function testMoveBlockThrowsBadStateExceptionWithNoPlaceholder(): void
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'non_existing',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Containers cannot be placed inside containers.
     */
    public function testMoveBlockThrowsBadStateExceptionWithContainerInsideContainer(): void
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(33),
            $this->blockService->loadBlockDraft(38),
            'main',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testMoveBlockThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'left',
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. You can only move block to blocks in the same layout.
     */
    public function testMoveBlockThrowsBadStateExceptionWhenTargetBlockIsInDifferentLayout(): void
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(31),
            $this->blockService->loadBlockDraft(33),
            'left',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToZone(): void
    {
        $movedBlock = $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(32),
            $this->layoutService->loadZoneDraft(1, 'left'),
            0
        );

        self::assertTrue($movedBlock->isDraft());
        self::assertSame(32, $movedBlock->getId());

        $zone = $this->layoutService->loadZoneDraft(1, 'left');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        self::assertSame($movedBlock->getId(), $blocks[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToDifferentZone(): void
    {
        $movedBlock = $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(32),
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        self::assertTrue($movedBlock->isDraft());
        self::assertSame(32, $movedBlock->getId());

        $zone = $this->layoutService->loadZoneDraft(1, 'right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        self::assertSame($movedBlock->getId(), $blocks[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be moved.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlock(31),
            $this->layoutService->loadZoneDraft(1, 'left'),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. You can only move blocks in draft zones.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZone(1, 'left'),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'left'),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. You can only move block to zone in the same layout.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWhenZoneIsInDifferentLayout(): void
    {
        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(32),
            $this->layoutService->loadZoneDraft(2, 'bottom'),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Block is not allowed in specified zone.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWithDisallowedIdentifier(): void
    {
        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'bottom'),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     */
    public function testRestoreBlock(): void
    {
        $blockHandler = $this->persistenceHandler->getBlockHandler();

        $block = $this->blockService->loadBlockDraft(31);

        // Move block so we can make sure position is kept while restoring the block.

        $zone = $this->layoutService->loadZoneDraft($block->getLayoutId(), 'left');
        $movedBlock = $this->blockService->moveBlockToZone($block, $zone, 1);
        $movedPersistenceBlock = $blockHandler->loadBlock($movedBlock->getId(), $movedBlock->getStatus());

        $restoredBlock = $this->blockService->restoreBlock($movedBlock);

        self::assertTrue($restoredBlock->isDraft());
        self::assertSame('grid', $restoredBlock->getViewType());
        self::assertSame('standard_with_intro', $restoredBlock->getItemViewType());
        self::assertSame('My published block', $restoredBlock->getName());

        self::assertSame('some-class', $restoredBlock->getParameter('css_class')->getValue());
        self::assertNull($restoredBlock->getParameter('css_id')->getValue());

        $collections = $restoredBlock->getCollections();
        self::assertCount(2, $collections);
        self::assertArrayHasKey('default', $collections);
        self::assertArrayHasKey('featured', $collections);

        self::assertSame(2, $collections['default']->getId());
        self::assertSame(3, $collections['featured']->getId());

        $restoredPersistenceBlock = $blockHandler->loadBlock($restoredBlock->getId(), $restoredBlock->getStatus());

        // Make sure the position is not moved.

        self::assertSame($movedPersistenceBlock->layoutId, $restoredPersistenceBlock->layoutId);
        self::assertSame($movedPersistenceBlock->depth, $restoredPersistenceBlock->depth);
        self::assertSame($movedPersistenceBlock->parentId, $restoredPersistenceBlock->parentId);
        self::assertSame($movedPersistenceBlock->placeholder, $restoredPersistenceBlock->placeholder);
        self::assertSame($movedPersistenceBlock->position, $restoredPersistenceBlock->position);
        self::assertSame($movedPersistenceBlock->path, $restoredPersistenceBlock->path);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     */
    public function testRestoreBlockRestoresMissingTranslations(): void
    {
        $block = $this->blockService->loadBlockDraft(31);

        $layout = $this->layoutService->loadLayoutDraft(1);
        $this->layoutService->addTranslation($layout, 'de', 'en');

        $restoredBlock = $this->blockService->restoreBlock($block);

        self::assertTrue($restoredBlock->isDraft());
        self::assertTrue($restoredBlock->isTranslatable());
        self::assertCount(3, $restoredBlock->getAvailableLocales());
        self::assertContains('en', $restoredBlock->getAvailableLocales());
        self::assertContains('hr', $restoredBlock->getAvailableLocales());
        self::assertContains('de', $restoredBlock->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be restored.
     */
    public function testRestoreBlockThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $block = $this->blockService->loadBlock(31);

        $this->blockService->restoreBlock($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     */
    public function testEnableTranslations(): void
    {
        $block = $this->blockService->loadBlockDraft(37);

        $updatedBlock = $this->blockService->enableTranslations($block);

        $layout = $this->layoutService->loadLayoutDraft($block->getLayoutId());
        foreach ($layout->getAvailableLocales() as $locale) {
            self::assertContains($locale, $updatedBlock->getAvailableLocales());
        }

        self::assertTrue($updatedBlock->isTranslatable());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. You can only enable translations for draft blocks.
     */
    public function testEnableTranslationsThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $block = $this->blockService->loadBlock(35);

        $this->blockService->enableTranslations($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Block is already translatable.
     */
    public function testEnableTranslationsThrowsBadStateExceptionWithEnabledTranslations(): void
    {
        $block = $this->blockService->loadBlockDraft(31);

        $this->blockService->enableTranslations($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage You can only enable translations if parent block is also translatable.
     */
    public function testEnableTranslationsThrowsBadStateExceptionWithNonTranslatableParentBlock(): void
    {
        $parentBlock = $this->blockService->loadBlockDraft(33);
        $this->blockService->disableTranslations($parentBlock);

        $block = $this->blockService->loadBlockDraft(37);

        $this->blockService->enableTranslations($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::disableTranslations
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalDisableTranslations
     */
    public function testDisableTranslations(): void
    {
        $block = $this->blockService->loadBlockDraft(31);

        $updatedBlock = $this->blockService->disableTranslations($block);

        self::assertFalse($updatedBlock->isTranslatable());

        self::assertNotContains('hr', $updatedBlock->getAvailableLocales());
        self::assertContains('en', $updatedBlock->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::disableTranslations
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalDisableTranslations
     */
    public function testDisableTranslationsOnContainer(): void
    {
        $block = $this->blockService->loadBlockDraft(33);
        $childBlock = $this->blockService->loadBlockDraft(37);

        $this->blockService->enableTranslations($childBlock);
        $block = $this->blockService->disableTranslations($block);

        self::assertFalse($block->isTranslatable());

        self::assertNotContains('hr', $block->getAvailableLocales());
        self::assertContains('en', $block->getAvailableLocales());

        $childBlock = $this->blockService->loadBlockDraft(37);

        self::assertFalse($childBlock->isTranslatable());

        self::assertNotContains('hr', $childBlock->getAvailableLocales());
        self::assertContains('en', $childBlock->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::disableTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. You can only disable translations for draft blocks.
     */
    public function testDisableTranslationsThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $block = $this->blockService->loadBlock(31);

        $this->blockService->disableTranslations($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::disableTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Block is not translatable.
     */
    public function testDisableTranslationsThrowsBadStateExceptionWithDisabledTranslations(): void
    {
        $block = $this->blockService->loadBlockDraft(35);

        $this->blockService->disableTranslations($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "31"
     */
    public function testDeleteBlock(): void
    {
        $block = $this->blockService->loadBlockDraft(31);
        $this->blockService->deleteBlock($block);

        $this->blockService->loadBlockDraft($block->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be deleted.
     */
    public function testDeleteThrowsBadStateExceptionBlockWithNonDraftBlock(): void
    {
        $block = $this->blockService->loadBlock(31);
        $this->blockService->deleteBlock($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct(): void
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition('title');

        $struct = $this->blockService->newBlockCreateStruct($blockDefinition);

        self::assertSame(
            [
                'viewType' => 'small',
                'itemViewType' => 'standard',
                'name' => null,
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'definition' => $blockDefinition,
                'collectionCreateStructs' => [],
                'parameterValues' => [
                    'css_class' => 'some-class',
                    'css_id' => null,
                ],
                'configStructs' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStruct(): void
    {
        $struct = $this->blockService->newBlockUpdateStruct('en');

        self::assertSame(
            [
                'locale' => 'en',
                'viewType' => null,
                'itemViewType' => null,
                'name' => null,
                'alwaysAvailable' => null,
                'parameterValues' => [],
                'configStructs' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStructFromBlock(): void
    {
        $block = $this->blockService->loadBlockDraft(36);
        $struct = $this->blockService->newBlockUpdateStruct('en', $block);

        self::assertArrayHasKey('key', $struct->getConfigStructs());

        self::assertSame(
            [
                'locale' => 'en',
                'viewType' => 'title',
                'itemViewType' => 'standard',
                'name' => 'My sixth block',
                'alwaysAvailable' => true,
                'parameterValues' => [
                    'css_class' => 'CSS class',
                    'css_id' => null,
                ],
                'configStructs' => [
                    'key' => [
                        'parameterValues' => [
                            'param1' => null,
                            'param2' => null,
                        ],
                    ],
                ],
            ],
            $this->exportObject($struct, true)
        );
    }
}
