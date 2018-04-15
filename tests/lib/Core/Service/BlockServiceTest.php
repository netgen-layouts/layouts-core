<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;

abstract class BlockServiceTest extends ServiceTestCase
{
    public function setUp()
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
    public function testLoadBlock()
    {
        $block = $this->blockService->loadBlock(31);

        $this->assertTrue($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "999999"
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $this->blockService->loadBlock(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "1"
     */
    public function testLoadBlockThrowsNotFoundExceptionOnLoadingInternalBlock()
    {
        $this->blockService->loadBlock(1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     */
    public function testLoadBlockDraft()
    {
        $block = $this->blockService->loadBlockDraft(31);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "999999"
     */
    public function testLoadBlockDraftThrowsNotFoundException()
    {
        $this->blockService->loadBlockDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "1"
     */
    public function testLoadBlockDraftThrowsNotFoundExceptionOnLoadingInternalBlock()
    {
        $this->blockService->loadBlockDraft(1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadZoneBlocks
     */
    public function testLoadZoneBlocks()
    {
        $blocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZone(1, 'right')
        );

        $this->assertCount(2, $blocks);
        foreach ($blocks as $block) {
            $this->assertInstanceOf(Block::class, $block);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadLayoutBlocks
     */
    public function testLoadLayoutBlocks()
    {
        $blocks = $this->blockService->loadLayoutBlocks(
            $this->layoutService->loadLayout(1)
        );

        $this->assertCount(3, $blocks);
        foreach ($blocks as $block) {
            $this->assertInstanceOf(Block::class, $block);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::hasPublishedState
     */
    public function testHasPublishedState()
    {
        $block = $this->blockService->loadBlock(31);

        $this->assertTrue($this->blockService->hasPublishedState($block));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::hasPublishedState
     */
    public function testHasPublishedStateReturnsFalse()
    {
        $block = $this->blockService->loadBlockDraft(36);

        $this->assertFalse($this->blockService->hasPublishedState($block));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlock()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $targetBlock = $this->blockService->loadBlockDraft(33);

        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0);

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $leftPlaceholder = $targetBlock->getPlaceholder('left');

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals($block->getId(), $leftPlaceholder->getBlocks()[0]->getId());

        $this->assertEquals(37, $leftPlaceholder->getBlocks()[1]->getId());

        $this->assertFalse($block->isTranslatable());
        $this->assertTrue($block->isAlwaysAvailable());
        $this->assertContains('en', $block->getAvailableLocales());
        $this->assertEquals('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithCollection()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $blockCreateStruct->addCollectionCreateStruct('default', new CollectionCreateStruct());

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);

        $collections = $block->getCollections();
        $this->assertCount(1, $collections);
        $this->assertArrayHasKey('default', $collections);

        $this->assertEquals(0, $collections['default']->getOffset());
        $this->assertNull($collections['default']->getLimit());

        $collection = $this->collectionService->loadCollectionDraft(7);
        $this->assertEquals(Collection::TYPE_MANUAL, $collection->getType());
        $this->assertFalse($collection->hasQuery());

        $this->assertEquals($block->isTranslatable(), $collection->isTranslatable());
        $this->assertEquals($block->isAlwaysAvailable(), $collection->isAlwaysAvailable());
        $this->assertEquals($block->getAvailableLocales(), $collection->getAvailableLocales());
        $this->assertEquals($block->getMainLocale(), $collection->getMainLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithDynamicCollection()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $blockCreateStruct->isTranslatable = true;

        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->queryType = $this->queryTypeRegistry->getQueryType('ezcontent_search');

        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->queryCreateStruct = $queryCreateStruct;

        $blockCreateStruct->addCollectionCreateStruct('default', $collectionCreateStruct);

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);

        $collections = $block->getCollections();
        $this->assertCount(1, $collections);
        $this->assertArrayHasKey('default', $collections);

        $this->assertEquals(0, $collections['default']->getOffset());
        $this->assertNull($collections['default']->getLimit());

        $collection = $this->collectionService->loadCollectionDraft(7);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $collection->getType());
        $this->assertTrue($collection->hasQuery());
        $this->assertInstanceOf(Query::class, $collection->getQuery());
        $this->assertEquals('ezcontent_search', $collection->getQuery()->getQueryType()->getType());

        $this->assertEquals($block->isTranslatable(), $collection->isTranslatable());
        $this->assertEquals($block->isAlwaysAvailable(), $collection->isAlwaysAvailable());
        $this->assertEquals($block->getAvailableLocales(), $collection->getAvailableLocales());
        $this->assertEquals($block->getMainLocale(), $collection->getMainLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateTranslatableBlock()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $blockCreateStruct->isTranslatable = true;

        $zone = $this->layoutService->loadZoneDraft(1, 'left');

        $block = $this->blockService->createBlockInZone($blockCreateStruct, $zone, 0);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);

        $this->assertTrue($block->isTranslatable());
        $this->assertEquals('en', $block->getMainLocale());

        $this->assertCount(2, $block->getAvailableLocales());
        $this->assertContains('en', $block->getAvailableLocales());
        $this->assertContains('hr', $block->getAvailableLocales());

        $this->assertEquals('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     */
    public function testCreateTranslatableBlockWithNonTranslatableTargetBlock()
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

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);

        $this->assertFalse($block->isTranslatable());
        $this->assertEquals('en', $block->getMainLocale());

        $this->assertCount(1, $block->getAvailableLocales());
        $this->assertContains('en', $block->getAvailableLocales());
        $this->assertNotContains('hr', $block->getAvailableLocales());

        $this->assertEquals('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithConfig()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $httpCacheConfigStruct = new ConfigStruct();
        $httpCacheConfigStruct->setParameterValue('use_http_cache', true);
        $httpCacheConfigStruct->setParameterValue('shared_max_age', 400);

        $blockCreateStruct->setConfigStruct(
            'http_cache',
            $httpCacheConfigStruct
        );

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);

        $this->assertTrue($block->hasConfig('http_cache'));
        $httpCacheConfig = $block->getConfig('http_cache');

        $this->assertInstanceOf(Config::class, $httpCacheConfig);
        $this->assertTrue($httpCacheConfig->getParameter('use_http_cache')->getValue());
        $this->assertEquals(400, $httpCacheConfig->getParameter('shared_max_age')->getValue());

        $this->assertFalse($block->isTranslatable());
        $this->assertContains('en', $block->getAvailableLocales());
        $this->assertEquals('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Blocks can only be created in blocks in draft status.
     */
    public function testCreateBlockThrowsBadStateExceptionWithNonDraftTargetBlock()
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
    public function testCreateBlockThrowsBadStateExceptionWithNonContainerTargetBlock()
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
    public function testCreateBlockThrowsBadStateExceptionWithNoPlaceholder()
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
    public function testCreateBlockWithNoPosition()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $targetBlock = $this->blockService->loadBlockDraft(33);

        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left');

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $leftPlaceholder = $targetBlock->getPlaceholder('left');

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals($block->getId(), $leftPlaceholder->getBlocks()[1]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCreateBlockThrowsBadStateExceptionWhenPositionIsTooLarge()
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
    public function testCreateBlockThrowsBadStateExceptionWithContainerInsideContainer()
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
    public function testCreateBlockInZone()
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

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals($block->getId(), $blocks[0]->getId());

        $this->assertEquals(31, $blocks[1]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithContainerBlock()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('column')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'left'),
            0
        );

        $this->assertCount(2, $block->getPlaceholders());

        $this->assertTrue($block->hasPlaceholder('main'));
        $this->assertTrue($block->hasPlaceholder('other'));

        $this->assertInstanceOf(Placeholder::class, $block->getPlaceholder('main'));
        $this->assertInstanceOf(Placeholder::class, $block->getPlaceholder('other'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithoutCollection()
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

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals($block->getId(), $blocks[0]->getId());

        $this->assertEquals(31, $blocks[1]->getId());

        $collections = $block->getCollections();
        $this->assertCount(0, $collections);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWhichDoesNotExistInLayoutType()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(7, 'center'),
            0
        );

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Blocks can only be created in zones in draft status.
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWithNonDraftZone()
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
    public function testCreateBlockInZoneWithNonExistentLayoutType()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(2, 'top')
        );

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithNoPosition()
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

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals($block->getId(), $blocks[2]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWhenPositionIsTooLarge()
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
    public function testCreateBlockInZoneThrowsBadStateExceptionWithWithDisallowedIdentifier()
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
    public function testUpdateBlock()
    {
        $block = $this->blockService->loadBlockDraft(31, array('en'));

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('hr');
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals('small', $block->getViewType());
        $this->assertEquals('Super cool block', $block->getName());

        $this->assertEquals('css-class', $block->getParameter('css_class')->getValue());
        $this->assertEquals('css-id', $block->getParameter('css_id')->getValue());

        $croBlock = $this->blockService->loadBlockDraft(31, array('hr'));

        $this->assertEquals('test_value', $croBlock->getParameter('css_class')->getValue());

        // CSS ID is untranslatable, meaning it keeps the value from main locale
        $this->assertEquals('css-id', $croBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockInMainLocale()
    {
        $block = $this->blockService->loadBlockDraft(31, array('en'));

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals('small', $block->getViewType());
        $this->assertEquals('Super cool block', $block->getName());

        $this->assertEquals('test_value', $block->getParameter('css_class')->getValue());
        $this->assertEquals('some_other_test_value', $block->getParameter('css_id')->getValue());

        $croBlock = $this->blockService->loadBlockDraft(31, array('hr'));

        $this->assertEquals('css-class-hr', $croBlock->getParameter('css_class')->getValue());

        // CSS ID is untranslatable, meaning it receives the value from the main locale
        $this->assertEquals('some_other_test_value', $croBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithUntranslatableParameters()
    {
        $block = $this->blockService->loadBlockDraft(31, array('en'));

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');
        $blockUpdateStruct->setParameterValue('css_class', 'english_css');

        $this->blockService->updateBlock($block, $blockUpdateStruct);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('hr');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value_2');
        $blockUpdateStruct->setParameterValue('css_class', 'croatian_css');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $croBlock = $this->blockService->loadBlockDraft(31, array('hr'));

        $this->assertEquals('english_css', $block->getParameter('css_class')->getValue());
        $this->assertEquals('some_other_test_value', $block->getParameter('css_id')->getValue());

        $this->assertEquals('croatian_css', $croBlock->getParameter('css_class')->getValue());
        $this->assertEquals('some_other_test_value', $croBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithConfig()
    {
        $block = $this->blockService->loadBlockDraft(32);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('hr');

        $httpCacheConfigStruct = new ConfigStruct();
        $httpCacheConfigStruct->setParameterValue('use_http_cache', true);
        $httpCacheConfigStruct->setParameterValue('shared_max_age', 400);

        $blockUpdateStruct->setConfigStruct('http_cache', $httpCacheConfigStruct);

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);

        $this->assertTrue($block->hasConfig('http_cache'));
        $httpCacheConfig = $block->getConfig('http_cache');

        $this->assertInstanceOf(Config::class, $httpCacheConfig);
        $this->assertTrue($httpCacheConfig->getParameter('use_http_cache')->getValue());
        $this->assertEquals(400, $httpCacheConfig->getParameter('shared_max_age')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithBlankName()
    {
        $block = $this->blockService->loadBlockDraft(31);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals('small', $block->getViewType());
        $this->assertEquals('My block', $block->getName());

        $this->assertEquals('test_value', $block->getParameter('css_class')->getValue());
        $this->assertEquals('some_other_test_value', $block->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithBlankViewType()
    {
        $block = $this->blockService->loadBlockDraft(31);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals('list', $block->getViewType());
        $this->assertEquals('Super cool block', $block->getName());

        $this->assertEquals('test_value', $block->getParameter('css_class')->getValue());
        $this->assertEquals('some_other_test_value', $block->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be updated.
     */
    public function testUpdateBlockThrowsBadStateExceptionWithNonDraftBlock()
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
    public function testUpdateBlockThrowsBadStateExceptionWithNonExistingLocale()
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
    public function testCopyBlock()
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'left'
        );

        $originalBlock = $this->blockService->loadBlockDraft(34);
        $this->assertEquals(0, $originalBlock->getParentPosition());

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(39, $copiedBlock->getId());
        $this->assertEquals(1, $copiedBlock->getParentPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockWithPosition()
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'left',
            1
        );

        $originalBlock = $this->blockService->loadBlockDraft(34);
        $this->assertEquals(0, $originalBlock->getParentPosition());

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(39, $copiedBlock->getId());
        $this->assertEquals(1, $copiedBlock->getParentPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockWithSamePosition()
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'left',
            0
        );

        $firstBlockInTargetBlock = $this->blockService->loadBlockDraft(37);
        $this->assertEquals(1, $firstBlockInTargetBlock->getParentPosition());

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(39, $copiedBlock->getId());
        $this->assertEquals(0, $copiedBlock->getParentPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCopyBlockThrowsBadStateExceptionWhenPositionIsTooLarge()
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
    public function testCopyBlockThrowsBadStateExceptionWithNonDraftBlock()
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
    public function testCopyBlockThrowsBadStateExceptionWithNonDraftTargetBlock()
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
    public function testCopyBlockThrowsBadStateExceptionWithNonContainerTargetBlock()
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
    public function testCopyBlockThrowsBadStateExceptionWithNoPlaceholder()
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
    public function testCopyBlockThrowsBadStateExceptionWithContainerInsideContainer()
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
    public function testCopyBlockThrowsBadStateExceptionWhenTargetBlockIsInDifferentLayout()
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
    public function testCopyBlockToZone()
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'right')
        );

        $originalBlock = $this->blockService->loadBlockDraft(31);
        $this->assertEquals(0, $originalBlock->getParentPosition());

        $secondBlock = $this->blockService->loadBlockDraft(35);
        $this->assertEquals(1, $secondBlock->getParentPosition());

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(39, $copiedBlock->getId());
        $this->assertEquals(2, $copiedBlock->getParentPosition());

        $copiedCollection = $this->collectionService->loadCollectionDraft(7);
        $this->assertFalse($copiedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $copiedCollection);

        $copiedCollection2 = $this->collectionService->loadCollectionDraft(8);
        $this->assertFalse($copiedCollection2->isPublished());
        $this->assertInstanceOf(Collection::class, $copiedCollection2);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithPosition()
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'right'),
            1
        );

        $originalBlock = $this->blockService->loadBlockDraft(31);
        $this->assertEquals(0, $originalBlock->getParentPosition());

        $secondBlock = $this->blockService->loadBlockDraft(35);
        $this->assertEquals(2, $secondBlock->getParentPosition());

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(39, $copiedBlock->getId());
        $this->assertEquals(1, $copiedBlock->getParentPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithSamePosition()
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        $originalBlock = $this->blockService->loadBlockDraft(31);
        $this->assertEquals(1, $originalBlock->getParentPosition());

        $secondBlock = $this->blockService->loadBlockDraft(35);
        $this->assertEquals(2, $secondBlock->getParentPosition());

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(39, $copiedBlock->getId());
        $this->assertEquals(0, $copiedBlock->getParentPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithLastPosition()
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'right'),
            2
        );

        $originalBlock = $this->blockService->loadBlockDraft(31);
        $this->assertEquals(0, $originalBlock->getParentPosition());

        $secondBlock = $this->blockService->loadBlockDraft(35);
        $this->assertEquals(1, $secondBlock->getParentPosition());

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(39, $copiedBlock->getId());
        $this->assertEquals(2, $copiedBlock->getParentPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithLowerPosition()
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(35),
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        $originalBlock = $this->blockService->loadBlockDraft(31);
        $this->assertEquals(1, $originalBlock->getParentPosition());

        $secondBlock = $this->blockService->loadBlockDraft(35);
        $this->assertEquals(2, $secondBlock->getParentPosition());

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(39, $copiedBlock->getId());
        $this->assertEquals(0, $copiedBlock->getParentPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWhenPositionIsTooLarge()
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
    public function testCopyBlockToZoneThrowsBadStateExceptionWithNonDraftBlock()
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
    public function testCopyBlockToZoneThrowsBadStateExceptionWithNonDraftZone()
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
    public function testCopyBlockToZoneThrowsBadStateExceptionWithDisallowedIdentifier()
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
    public function testCopyBlockToZoneThrowsBadStateExceptionWhenZoneIsInDifferentLayout()
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
    public function testMoveBlock()
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(34),
            $this->blockService->loadBlockDraft(33),
            'left',
            0
        );

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
        $this->assertEquals(34, $movedBlock->getId());

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $leftPlaceholder = $targetBlock->getPlaceholder('left');

        $this->assertEquals($movedBlock->getId(), $leftPlaceholder->getBlocks()[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockToDifferentPlaceholder()
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(37),
            $this->blockService->loadBlockDraft(33),
            'right',
            0
        );

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
        $this->assertEquals(37, $movedBlock->getId());

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $leftPlaceholder = $targetBlock->getPlaceholder('left');
        $rightPlaceholder = $targetBlock->getPlaceholder('right');

        $this->assertEmpty($leftPlaceholder->getBlocks());
        $this->assertEquals($movedBlock->getId(), $rightPlaceholder->getBlocks()[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockToDifferentBlock()
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(37),
            $this->blockService->loadBlockDraft(38),
            'main',
            0
        );

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
        $this->assertEquals(37, $movedBlock->getId());

        $originalBlock = $this->blockService->loadBlockDraft(33);
        $targetBlock = $this->blockService->loadBlockDraft(38);
        $originalPlaceholder = $originalBlock->getPlaceholder('left');
        $targetPlaceholder = $targetBlock->getPlaceholder('main');

        $this->assertEmpty($originalPlaceholder->getBlocks());
        $this->assertEquals($movedBlock->getId(), $targetPlaceholder->getBlocks()[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be moved.
     */
    public function testMoveBlockThrowsBadStateExceptionWithNonDraftBlock()
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
    public function testMoveBlockThrowsBadStateExceptionWithNonDraftTargetBlock()
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
    public function testMoveBlockThrowsBadStateExceptionWhenTargetBlockIsNotContainer()
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
    public function testMoveBlockThrowsBadStateExceptionWithNoPlaceholder()
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
    public function testMoveBlockThrowsBadStateExceptionWithContainerInsideContainer()
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
    public function testMoveBlockThrowsBadStateExceptionWhenPositionIsTooLarge()
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
    public function testMoveBlockThrowsBadStateExceptionWhenTargetBlockIsInDifferentLayout()
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
    public function testMoveBlockToZone()
    {
        $movedBlock = $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(32),
            $this->layoutService->loadZoneDraft(1, 'left'),
            0
        );

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
        $this->assertEquals(32, $movedBlock->getId());

        $zone = $this->layoutService->loadZoneDraft(1, 'left');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        $this->assertEquals($movedBlock->getId(), $blocks[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToDifferentZone()
    {
        $movedBlock = $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(32),
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
        $this->assertEquals(32, $movedBlock->getId());

        $zone = $this->layoutService->loadZoneDraft(1, 'right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        $this->assertEquals($movedBlock->getId(), $blocks[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be moved.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWithNonDraftBlock()
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
    public function testMoveBlockToZoneThrowsBadStateExceptionWithNonDraftZone()
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
    public function testMoveBlockToZoneThrowsBadStateExceptionWhenPositionIsTooLarge()
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
    public function testMoveBlockToZoneThrowsBadStateExceptionWhenZoneIsInDifferentLayout()
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
    public function testMoveBlockToZoneThrowsBadStateExceptionWithDisallowedIdentifier()
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
    public function testRestoreBlock()
    {
        $blockHandler = $this->persistenceHandler->getBlockHandler();

        $block = $this->blockService->loadBlockDraft(31);

        // Move block so we can make sure position is kept while restoring the block.

        $zone = $this->layoutService->loadZoneDraft($block->getLayoutId(), 'left');
        $movedBlock = $this->blockService->moveBlockToZone($block, $zone, 1);
        $movedPersistenceBlock = $blockHandler->loadBlock($movedBlock->getId(), $movedBlock->getStatus());

        $restoredBlock = $this->blockService->restoreBlock($movedBlock);

        $this->assertFalse($restoredBlock->isPublished());
        $this->assertInstanceOf(Block::class, $restoredBlock);
        $this->assertEquals('grid', $restoredBlock->getViewType());
        $this->assertEquals('standard_with_intro', $restoredBlock->getItemViewType());
        $this->assertEquals('My published block', $restoredBlock->getName());

        $this->assertEquals('some-class', $restoredBlock->getParameter('css_class')->getValue());
        $this->assertNull($restoredBlock->getParameter('css_id')->getValue());

        $collections = $restoredBlock->getCollections();
        $this->assertCount(2, $collections);
        $this->assertArrayHasKey('default', $collections);
        $this->assertArrayHasKey('featured', $collections);

        $this->assertEquals(2, $collections['default']->getId());
        $this->assertEquals(3, $collections['featured']->getId());

        $restoredPersistenceBlock = $blockHandler->loadBlock($restoredBlock->getId(), $restoredBlock->getStatus());

        // Make sure the position is not moved.

        $this->assertEquals($movedPersistenceBlock->layoutId, $restoredPersistenceBlock->layoutId);
        $this->assertEquals($movedPersistenceBlock->depth, $restoredPersistenceBlock->depth);
        $this->assertEquals($movedPersistenceBlock->parentId, $restoredPersistenceBlock->parentId);
        $this->assertEquals($movedPersistenceBlock->placeholder, $restoredPersistenceBlock->placeholder);
        $this->assertEquals($movedPersistenceBlock->position, $restoredPersistenceBlock->position);
        $this->assertEquals($movedPersistenceBlock->path, $restoredPersistenceBlock->path);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     */
    public function testRestoreBlockRestoresMissingTranslations()
    {
        $block = $this->blockService->loadBlockDraft(31);

        $layout = $this->layoutService->loadLayoutDraft(1);
        $this->layoutService->addTranslation($layout, 'de', 'en');

        $restoredBlock = $this->blockService->restoreBlock($block);

        $this->assertFalse($restoredBlock->isPublished());
        $this->assertInstanceOf(Block::class, $restoredBlock);
        $this->assertTrue($restoredBlock->isTranslatable());

        $this->assertCount(3, $restoredBlock->getAvailableLocales());
        $this->assertContains('en', $restoredBlock->getAvailableLocales());
        $this->assertContains('hr', $restoredBlock->getAvailableLocales());
        $this->assertContains('de', $restoredBlock->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be restored.
     */
    public function testRestoreBlockThrowsBadStateExceptionWithNonDraftBlock()
    {
        $block = $this->blockService->loadBlock(31);

        $this->blockService->restoreBlock($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     */
    public function testEnableTranslations()
    {
        $block = $this->blockService->loadBlockDraft(37);

        $updatedBlock = $this->blockService->enableTranslations($block);

        $layout = $this->layoutService->loadLayoutDraft($block->getLayoutId());
        foreach ($layout->getAvailableLocales() as $locale) {
            $this->assertContains($locale, $updatedBlock->getAvailableLocales());
        }

        $this->assertTrue($updatedBlock->isTranslatable());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. You can only enable translations for draft blocks.
     */
    public function testEnableTranslationsThrowsBadStateExceptionWithNonDraftBlock()
    {
        $block = $this->blockService->loadBlock(35);

        $this->blockService->enableTranslations($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Block is already translatable.
     */
    public function testEnableTranslationsThrowsBadStateExceptionWithEnabledTranslations()
    {
        $block = $this->blockService->loadBlockDraft(31);

        $this->blockService->enableTranslations($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::enableTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage You can only enable translations if parent block is also translatable.
     */
    public function testEnableTranslationsThrowsBadStateExceptionWithNonTranslatableParentBlock()
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
    public function testDisableTranslations()
    {
        $block = $this->blockService->loadBlockDraft(31);

        $updatedBlock = $this->blockService->disableTranslations($block);

        $this->assertFalse($updatedBlock->isTranslatable());

        $this->assertNotContains('hr', $updatedBlock->getAvailableLocales());
        $this->assertContains('en', $updatedBlock->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::disableTranslations
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalDisableTranslations
     */
    public function testDisableTranslationsOnContainer()
    {
        $block = $this->blockService->loadBlockDraft(33);
        $childBlock = $this->blockService->loadBlockDraft(37);

        $this->blockService->enableTranslations($childBlock);
        $block = $this->blockService->disableTranslations($block);

        $this->assertFalse($block->isTranslatable());

        $this->assertNotContains('hr', $block->getAvailableLocales());
        $this->assertContains('en', $block->getAvailableLocales());

        $childBlock = $this->blockService->loadBlockDraft(37);

        $this->assertFalse($childBlock->isTranslatable());

        $this->assertNotContains('hr', $childBlock->getAvailableLocales());
        $this->assertContains('en', $childBlock->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::disableTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. You can only disable translations for draft blocks.
     */
    public function testDisableTranslationsThrowsBadStateExceptionWithNonDraftBlock()
    {
        $block = $this->blockService->loadBlock(31);

        $this->blockService->disableTranslations($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::disableTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Block is not translatable.
     */
    public function testDisableTranslationsThrowsBadStateExceptionWithDisabledTranslations()
    {
        $block = $this->blockService->loadBlockDraft(35);

        $this->blockService->disableTranslations($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "31"
     */
    public function testDeleteBlock()
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
    public function testDeleteThrowsBadStateExceptionBlockWithNonDraftBlock()
    {
        $block = $this->blockService->loadBlock(31);
        $this->blockService->deleteBlock($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct()
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition('title');

        $this->assertEquals(
            new BlockCreateStruct(
                array(
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'definition' => $blockDefinition,
                    'viewType' => 'small',
                    'itemViewType' => 'standard',
                    'parameterValues' => array(
                        'css_class' => 'some-class',
                        'css_id' => null,
                    ),
                )
            ),
            $this->blockService->newBlockCreateStruct($blockDefinition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStruct()
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->locale = 'en';

        $this->assertEquals(
            $blockUpdateStruct,
            $this->blockService->newBlockUpdateStruct('en')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStructFromBlock()
    {
        $block = $this->blockService->loadBlockDraft(36);

        $this->assertEquals(
            new BlockUpdateStruct(
                array(
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => $block->getViewType(),
                    'itemViewType' => $block->getItemViewType(),
                    'name' => $block->getName(),
                    'parameterValues' => array(
                        'css_class' => 'CSS class',
                        'css_id' => null,
                    ),
                    'configStructs' => array(
                        'http_cache' => new ConfigStruct(
                            array(
                                'parameterValues' => array(
                                    'use_http_cache' => null,
                                    'shared_max_age' => null,
                                ),
                            )
                        ),
                    ),
                )
            ),
            $this->blockService->newBlockUpdateStruct('en', $block)
        );
    }
}
