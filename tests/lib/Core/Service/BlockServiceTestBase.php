<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\UuidGeneratorTrait;
use Ramsey\Uuid\Uuid;

abstract class BlockServiceTestBase extends CoreTestCase
{
    use ExportObjectTrait;
    use UuidGeneratorTrait;

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::__construct
     * @covers \Netgen\Layouts\Core\Service\BlockService::loadBlock
     */
    public function testLoadBlock(): void
    {
        $block = $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        self::assertTrue($block->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::loadBlock
     */
    public function testLoadBlockThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->blockService->loadBlock(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::loadBlock
     */
    public function testLoadBlockThrowsNotFoundExceptionOnLoadingInternalBlock(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find block with identifier "01f0c14e-2e15-54a1-8b41-58a3a8a9a917"');

        $this->blockService->loadBlock(Uuid::fromString('01f0c14e-2e15-54a1-8b41-58a3a8a9a917'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::__construct
     * @covers \Netgen\Layouts\Core\Service\BlockService::loadBlockDraft
     */
    public function testLoadBlockDraft(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        self::assertTrue($block->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::loadBlockDraft
     */
    public function testLoadBlockDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->blockService->loadBlockDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::loadBlockDraft
     */
    public function testLoadBlockDraftThrowsNotFoundExceptionOnLoadingInternalBlock(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find block with identifier "01f0c14e-2e15-54a1-8b41-58a3a8a9a917"');

        $this->blockService->loadBlockDraft(Uuid::fromString('01f0c14e-2e15-54a1-8b41-58a3a8a9a917'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::filterUntranslatedBlocks
     * @covers \Netgen\Layouts\Core\Service\BlockService::loadZoneBlocks
     */
    public function testLoadZoneBlocks(): void
    {
        $blocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
        );

        self::assertCount(2, $blocks);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::filterUntranslatedBlocks
     * @covers \Netgen\Layouts\Core\Service\BlockService::loadPlaceholderBlocks
     */
    public function testLoadPlaceholderBlocks(): void
    {
        $blocks = $this->blockService->loadPlaceholderBlocks(
            $this->blockService->loadBlock(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
        );

        self::assertCount(1, $blocks);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::filterUntranslatedBlocks
     * @covers \Netgen\Layouts\Core\Service\BlockService::loadLayoutBlocks
     */
    public function testLoadLayoutBlocks(): void
    {
        $blocks = $this->blockService->loadLayoutBlocks(
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );

        self::assertCount(3, $blocks);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::hasPublishedState
     */
    public function testHasPublishedState(): void
    {
        $block = $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        self::assertTrue($this->blockService->hasPublishedState($block));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::hasPublishedState
     */
    public function testHasPublishedStateReturnsFalse(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('b40aa688-b8e8-5e07-bf82-4a97e5ed8bad'));

        self::assertFalse($this->blockService->hasPublishedState($block));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlock(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        $targetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));

        /** @var \Netgen\Layouts\API\Values\Block\Block $block */
        $block = $this->withUuids(
            fn (): Block => $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        $targetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));
        $leftPlaceholder = $targetBlock->getPlaceholder('left');

        $firstBlock = $leftPlaceholder->getBlocks()[0];
        $secondBlock = $leftPlaceholder->getBlocks()[1];

        self::assertInstanceOf(Block::class, $firstBlock);
        self::assertInstanceOf(Block::class, $secondBlock);

        self::assertTrue($block->isDraft());
        self::assertSame($block->getId()->toString(), $firstBlock->getId()->toString());
        self::assertSame('129f51de-a535-5094-8517-45d672e06302', $secondBlock->getId()->toString());

        self::assertFalse($block->isTranslatable());
        self::assertTrue($block->isAlwaysAvailable());
        self::assertContains('en', $block->getAvailableLocales());
        self::assertSame('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithCollection(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        $blockCreateStruct->addCollectionCreateStruct('default', new CollectionCreateStruct());
        $targetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));

        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0);

        self::assertTrue($block->isDraft());

        $collections = $block->getCollections();
        self::assertCount(1, $collections);
        self::assertArrayHasKey('default', $collections);

        /** @var \Netgen\Layouts\API\Values\Collection\Collection $defaultCollection */
        $defaultCollection = $collections['default'];

        self::assertSame(0, $defaultCollection->getOffset());
        self::assertNull($defaultCollection->getLimit());
        self::assertFalse($defaultCollection->hasQuery());
        self::assertSame($block->isTranslatable(), $defaultCollection->isTranslatable());
        self::assertSame($block->isAlwaysAvailable(), $defaultCollection->isAlwaysAvailable());
        self::assertSame($block->getAvailableLocales(), $defaultCollection->getAvailableLocales());
        self::assertSame($block->getMainLocale(), $defaultCollection->getMainLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithDynamicCollection(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        $blockCreateStruct->isTranslatable = true;

        $queryCreateStruct = new QueryCreateStruct(
            $this->queryTypeRegistry->getQueryType('my_query_type'),
        );

        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->queryCreateStruct = $queryCreateStruct;

        $blockCreateStruct->addCollectionCreateStruct('default', $collectionCreateStruct);

        $targetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));
        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left', 0);

        self::assertTrue($block->isDraft());

        $collections = $block->getCollections();
        self::assertCount(1, $collections);
        self::assertArrayHasKey('default', $collections);

        /** @var \Netgen\Layouts\API\Values\Collection\Collection $defaultCollection */
        $defaultCollection = $collections['default'];

        self::assertSame(0, $defaultCollection->getOffset());
        self::assertNull($defaultCollection->getLimit());
        self::assertTrue($defaultCollection->hasQuery());
        self::assertInstanceOf(Query::class, $defaultCollection->getQuery());
        self::assertSame('my_query_type', $defaultCollection->getQuery()->getQueryType()->getType());
        self::assertSame($block->isTranslatable(), $defaultCollection->isTranslatable());
        self::assertSame($block->isAlwaysAvailable(), $defaultCollection->isAlwaysAvailable());
        self::assertSame($block->getAvailableLocales(), $defaultCollection->getAvailableLocales());
        self::assertSame($block->getMainLocale(), $defaultCollection->getMainLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateTranslatableBlock(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        $blockCreateStruct->isTranslatable = true;

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left');

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
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     */
    public function testCreateTranslatableBlockWithNonTranslatableTargetBlock(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title'),
        );

        $targetBlock = $this->blockService->disableTranslations(
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
        );

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $targetBlock,
            'left',
            0,
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
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithConfig(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('param1', true);
        $configStruct->setParameterValue('param2', 400);

        $blockCreateStruct->setConfigStruct(
            'key',
            $configStruct,
        );

        $targetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));
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
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     */
    public function testCreateBlockThrowsBadStateExceptionWithNonDraftTargetBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. Blocks can only be created in blocks in draft status.');

        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlock(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     */
    public function testCreateBlockThrowsBadStateExceptionWithNonContainerTargetBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. Target block is not a container.');

        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            'main',
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     */
    public function testCreateBlockThrowsBadStateExceptionWithNoPlaceholder(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.');

        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'non_existing',
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithNoPosition(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        $targetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));

        /** @var \Netgen\Layouts\API\Values\Block\Block $block */
        $block = $this->withUuids(
            fn (): Block => $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'left'),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        $targetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));
        $leftPlaceholder = $targetBlock->getPlaceholder('left');

        $secondBlock = $leftPlaceholder->getBlocks()[1];
        self::assertInstanceOf(Block::class, $secondBlock);

        self::assertTrue($block->isDraft());
        self::assertSame($block->getId()->toString(), $secondBlock->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     */
    public function testCreateBlockThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
            9999,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlock
     */
    public function testCreateBlockThrowsBadStateExceptionWithContainerInsideContainer(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "blockCreateStruct" has an invalid state. Containers cannot be placed inside containers.');

        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('column'),
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZone(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list'),
        );

        /** @var \Netgen\Layouts\API\Values\Block\Block $block */
        $block = $this->withUuids(
            fn (): Block => $this->blockService->createBlockInZone(
                $blockCreateStruct,
                $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
                0,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        self::assertInstanceOf(Block::class, $blocks[0]);
        self::assertInstanceOf(Block::class, $blocks[1]);

        self::assertTrue($block->isDraft());
        self::assertSame($block->getId()->toString(), $blocks[0]->getId()->toString());

        self::assertSame('28df256a-2467-5527-b398-9269ccc652de', $blocks[1]->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithContainerBlock(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('column'),
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left'),
            0,
        );

        self::assertCount(2, $block->getPlaceholders());

        self::assertTrue($block->hasPlaceholder('main'));
        self::assertTrue($block->hasPlaceholder('other'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithoutCollection(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title'),
        );

        /** @var \Netgen\Layouts\API\Values\Block\Block $block */
        $block = $this->withUuids(
            fn (): Block => $this->blockService->createBlockInZone(
                $blockCreateStruct,
                $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
                0,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        self::assertInstanceOf(Block::class, $blocks[0]);
        self::assertInstanceOf(Block::class, $blocks[1]);

        self::assertTrue($block->isDraft());
        self::assertSame($block->getId()->toString(), $blocks[0]->getId()->toString());
        self::assertSame('28df256a-2467-5527-b398-9269ccc652de', $blocks[1]->getId()->toString());

        $collections = $block->getCollections();
        self::assertCount(0, $collections);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWhichDoesNotExistInLayoutType(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title'),
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(Uuid::fromString('4b0202b3-5d06-5962-ae0c-bbeb25ee3503'))->getZone('center'),
            0,
        );

        self::assertTrue($block->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlockInZone
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Blocks can only be created in zones in draft status.');

        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title'),
        );

        $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithNonExistentLayoutType(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title'),
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top'),
        );

        self::assertTrue($block->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockInZoneWithNoPosition(): void
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title'),
        );

        /** @var \Netgen\Layouts\API\Values\Block\Block $block */
        $block = $this->withUuids(
            fn (): Block => $this->blockService->createBlockInZone(
                $blockCreateStruct,
                $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        self::assertInstanceOf(Block::class, $blocks[2]);

        self::assertTrue($block->isDraft());
        self::assertSame($block->getId()->toString(), $blocks[2]->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlockInZone
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title'),
        );

        $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
            9999,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::createBlockInZone
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWithWithDisallowedIdentifier(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Block is not allowed in specified zone.');

        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('gallery'),
        );

        $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetItemViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetSlotViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlock(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'), ['en']);

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

        $croBlock = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'), ['hr']);

        self::assertSame('test_value', $croBlock->getParameter('css_class')->getValue());

        // CSS ID is untranslatable, meaning it keeps the value from main locale
        self::assertSame('css-id', $croBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetItemViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetSlotViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockInMainLocale(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'), ['en']);

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

        $croBlock = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'), ['hr']);

        self::assertSame('css-class-hr', $croBlock->getParameter('css_class')->getValue());

        // CSS ID is untranslatable, meaning it receives the value from the main locale
        self::assertSame('some_other_test_value', $croBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetItemViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetSlotViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithUntranslatableParameters(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'), ['en']);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');
        $blockUpdateStruct->setParameterValue('css_class', 'english_css');

        $this->blockService->updateBlock($block, $blockUpdateStruct);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('hr');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value_2');
        $blockUpdateStruct->setParameterValue('css_class', 'croatian_css');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $croBlock = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'), ['hr']);

        self::assertSame('english_css', $block->getParameter('css_class')->getValue());
        self::assertSame('some_other_test_value', $block->getParameter('css_id')->getValue());

        self::assertSame('croatian_css', $croBlock->getParameter('css_class')->getValue());
        self::assertSame('some_other_test_value', $croBlock->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetItemViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetSlotViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithConfig(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('b07d3a85-bcdb-5af2-9b6f-deba36c700e7'));

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
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetItemViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetSlotViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithBlankName(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

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
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetItemViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::resetSlotViewTypes
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlockTranslations
     */
    public function testUpdateBlockWithBlankViewType(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

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
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlockThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Only draft blocks can be updated.');

        $block = $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('en');
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $this->blockService->updateBlock($block, $blockUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlockThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Block does not have the specified translation.');

        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct('de');
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $this->blockService->updateBlock($block, $blockUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlock(): void
    {
        /** @var \Netgen\Layouts\API\Values\Block\Block $copiedBlock */
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockService->copyBlock(
                $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
                $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
                'left',
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        $originalBlock = $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5'));
        self::assertSame(0, $originalBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedBlock->getId()->toString());
        self::assertSame(1, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockWithPosition(): void
    {
        /** @var \Netgen\Layouts\API\Values\Block\Block $copiedBlock */
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockService->copyBlock(
                $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
                $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
                'left',
                1,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        $originalBlock = $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5'));
        self::assertSame(0, $originalBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedBlock->getId()->toString());
        self::assertSame(1, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockWithSamePosition(): void
    {
        /** @var \Netgen\Layouts\API\Values\Block\Block $copiedBlock */
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockService->copyBlock(
                $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
                $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
                'left',
                0,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        $firstBlockInTargetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('129f51de-a535-5094-8517-45d672e06302'));
        self::assertSame(1, $firstBlockInTargetBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedBlock->getId()->toString());
        self::assertSame(0, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
            9999,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Only draft blocks can be copied.');

        $this->blockService->copyBlock(
            $this->blockService->loadBlock(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'main',
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockThrowsBadStateExceptionWithNonDraftTargetBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. You can only copy blocks to draft blocks.');

        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
            $this->blockService->loadBlock(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'main',
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockThrowsBadStateExceptionWithNonContainerTargetBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. Target block is not a container.');

        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
            $this->blockService->loadBlockDraft(Uuid::fromString('129f51de-a535-5094-8517-45d672e06302')),
            'main',
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockThrowsBadStateExceptionWithNoPlaceholder(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.');

        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'non_existing',
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockThrowsBadStateExceptionWithContainerInsideContainer(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Containers cannot be placed inside containers.');

        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            $this->blockService->loadBlockDraft(Uuid::fromString('a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6')),
            'main',
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockThrowsBadStateExceptionWhenTargetBlockIsInDifferentLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. You can only copy block to blocks in the same layout.');

        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZone(): void
    {
        /** @var \Netgen\Layouts\API\Values\Block\Block $copiedBlock */
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockService->copyBlockToZone(
                $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
                $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                '805895b2-6292-5243-a0c0-06a6ec0e28a2',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
                'f08717e5-5910-574d-b976-03d877c4729b',
                'e804ebd6-dc99-53bb-85d5-196d68933761',
                '910f4fe2-97b0-5599-8a45-8fb8a8e0ca6d',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                '3a17132d-9072-45f3-a0b3-b91bd4b0fcf3',
                '29f091e0-81cc-4bd3-aec5-673cd06abce5',
            ],
        );

        $originalBlock = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));
        self::assertSame(0, $originalBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(Uuid::fromString('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e'));
        self::assertSame(1, $secondBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedBlock->getId()->toString());
        self::assertSame(2, $copiedBlock->getPosition());

        self::assertTrue($copiedBlock->getCollection('default')->isDraft());
        self::assertTrue($copiedBlock->getCollection('featured')->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithPosition(): void
    {
        /** @var \Netgen\Layouts\API\Values\Block\Block $copiedBlock */
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockService->copyBlockToZone(
                $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
                $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
                1,
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                '805895b2-6292-5243-a0c0-06a6ec0e28a2',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
                'f08717e5-5910-574d-b976-03d877c4729b',
                'e804ebd6-dc99-53bb-85d5-196d68933761',
                '910f4fe2-97b0-5599-8a45-8fb8a8e0ca6d',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                '3a17132d-9072-45f3-a0b3-b91bd4b0fcf3',
                '29f091e0-81cc-4bd3-aec5-673cd06abce5',
            ],
        );

        $originalBlock = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));
        self::assertSame(0, $originalBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(Uuid::fromString('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e'));
        self::assertSame(2, $secondBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedBlock->getId()->toString());
        self::assertSame(1, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithSamePosition(): void
    {
        /** @var \Netgen\Layouts\API\Values\Block\Block $copiedBlock */
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockService->copyBlockToZone(
                $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
                $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
                0,
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                '805895b2-6292-5243-a0c0-06a6ec0e28a2',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
                'f08717e5-5910-574d-b976-03d877c4729b',
                'e804ebd6-dc99-53bb-85d5-196d68933761',
                '910f4fe2-97b0-5599-8a45-8fb8a8e0ca6d',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                '3a17132d-9072-45f3-a0b3-b91bd4b0fcf3',
                '29f091e0-81cc-4bd3-aec5-673cd06abce5',
            ],
        );

        $originalBlock = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));
        self::assertSame(1, $originalBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(Uuid::fromString('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e'));
        self::assertSame(2, $secondBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedBlock->getId()->toString());
        self::assertSame(0, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithLastPosition(): void
    {
        /** @var \Netgen\Layouts\API\Values\Block\Block $copiedBlock */
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockService->copyBlockToZone(
                $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
                $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
                2,
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                '805895b2-6292-5243-a0c0-06a6ec0e28a2',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
                'f08717e5-5910-574d-b976-03d877c4729b',
                'e804ebd6-dc99-53bb-85d5-196d68933761',
                '910f4fe2-97b0-5599-8a45-8fb8a8e0ca6d',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                '3a17132d-9072-45f3-a0b3-b91bd4b0fcf3',
                '29f091e0-81cc-4bd3-aec5-673cd06abce5',
            ],
        );

        $originalBlock = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));
        self::assertSame(0, $originalBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(Uuid::fromString('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e'));
        self::assertSame(1, $secondBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedBlock->getId()->toString());
        self::assertSame(2, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneWithLowerPosition(): void
    {
        /** @var \Netgen\Layouts\API\Values\Block\Block $copiedBlock */
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockService->copyBlockToZone(
                $this->blockService->loadBlockDraft(Uuid::fromString('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e')),
                $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
                0,
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
            ],
        );

        $originalBlock = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));
        self::assertSame(1, $originalBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(Uuid::fromString('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e'));
        self::assertSame(2, $secondBlock->getPosition());

        self::assertTrue($copiedBlock->isDraft());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedBlock->getId()->toString());
        self::assertSame(0, $copiedBlock->getPosition());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
            9999,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Only draft blocks can be copied.');

        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. You can only copy blocks in draft zones.');

        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWithDisallowedIdentifier(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Block is not allowed in specified zone.');

        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('bottom'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::copyBlockToZone
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWhenZoneIsInDifferentLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. You can only copy block to zone in the same layout.');

        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(Uuid::fromString('b07d3a85-bcdb-5af2-9b6f-deba36c700e7')),
            $this->layoutService->loadLayoutDraft(Uuid::fromString('8626a1ca-6413-5f54-acef-de7db06272ce'))->getZone('bottom'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlock(): void
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
            0,
        );

        self::assertTrue($movedBlock->isDraft());
        self::assertSame('42446cc9-24c3-573c-9022-6b3a764727b5', $movedBlock->getId()->toString());

        $targetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));
        $leftPlaceholder = $targetBlock->getPlaceholder('left');

        $firstBlock = $leftPlaceholder->getBlocks()[0];
        self::assertInstanceOf(Block::class, $firstBlock);

        self::assertSame($movedBlock->getId()->toString(), $firstBlock->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockToDifferentPlaceholder(): void
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('129f51de-a535-5094-8517-45d672e06302')),
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'right',
            0,
        );

        self::assertTrue($movedBlock->isDraft());
        self::assertSame('129f51de-a535-5094-8517-45d672e06302', $movedBlock->getId()->toString());

        $targetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));
        $leftPlaceholder = $targetBlock->getPlaceholder('left');
        $rightPlaceholder = $targetBlock->getPlaceholder('right');

        $firstBlock = $rightPlaceholder->getBlocks()[0];
        self::assertInstanceOf(Block::class, $firstBlock);

        self::assertEmpty($leftPlaceholder->getBlocks());
        self::assertSame($movedBlock->getId()->toString(), $firstBlock->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockToDifferentBlock(): void
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('129f51de-a535-5094-8517-45d672e06302')),
            $this->blockService->loadBlockDraft(Uuid::fromString('a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6')),
            'main',
            0,
        );

        self::assertTrue($movedBlock->isDraft());
        self::assertSame('129f51de-a535-5094-8517-45d672e06302', $movedBlock->getId()->toString());

        $originalBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));
        $targetBlock = $this->blockService->loadBlockDraft(Uuid::fromString('a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6'));
        $originalPlaceholder = $originalBlock->getPlaceholder('left');
        $targetPlaceholder = $targetBlock->getPlaceholder('main');

        $firstBlock = $targetPlaceholder->getBlocks()[0];
        self::assertInstanceOf(Block::class, $firstBlock);

        self::assertEmpty($originalPlaceholder->getBlocks());
        self::assertSame($movedBlock->getId()->toString(), $firstBlock->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Only draft blocks can be moved.');

        $this->blockService->moveBlock(
            $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockThrowsBadStateExceptionWithNonDraftTargetBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. You can only move blocks to draft blocks.');

        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->blockService->loadBlock(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockThrowsBadStateExceptionWhenTargetBlockIsNotContainer(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. Target block is not a container.');

        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('b07d3a85-bcdb-5af2-9b6f-deba36c700e7')),
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            'left',
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockThrowsBadStateExceptionWithNoPlaceholder(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.');

        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'non_existing',
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockThrowsBadStateExceptionWithContainerInsideContainer(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Containers cannot be placed inside containers.');

        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            $this->blockService->loadBlockDraft(Uuid::fromString('a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6')),
            'main',
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('42446cc9-24c3-573c-9022-6b3a764727b5')),
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
            9999,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockThrowsBadStateExceptionWhenTargetBlockIsInDifferentLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. You can only move block to blocks in the same layout.');

        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')),
            'left',
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToZone(): void
    {
        $movedBlock = $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(Uuid::fromString('b07d3a85-bcdb-5af2-9b6f-deba36c700e7')),
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left'),
            0,
        );

        self::assertTrue($movedBlock->isDraft());
        self::assertSame('b07d3a85-bcdb-5af2-9b6f-deba36c700e7', $movedBlock->getId()->toString());

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        self::assertInstanceOf(Block::class, $blocks[0]);

        self::assertSame($movedBlock->getId()->toString(), $blocks[0]->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToDifferentZone(): void
    {
        $movedBlock = $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(Uuid::fromString('b07d3a85-bcdb-5af2-9b6f-deba36c700e7')),
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
            0,
        );

        self::assertTrue($movedBlock->isDraft());
        self::assertSame('b07d3a85-bcdb-5af2-9b6f-deba36c700e7', $movedBlock->getId()->toString());

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        self::assertInstanceOf(Block::class, $blocks[0]);

        self::assertSame($movedBlock->getId()->toString(), $blocks[0]->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Only draft blocks can be moved.');

        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left'),
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. You can only move blocks in draft zones.');

        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left'),
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWhenPositionIsTooLarge(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left'),
            9999,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWhenZoneIsInDifferentLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. You can only move block to zone in the same layout.');

        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(Uuid::fromString('b07d3a85-bcdb-5af2-9b6f-deba36c700e7')),
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('bottom'),
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::moveBlockToZone
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWithDisallowedIdentifier(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Block is not allowed in specified zone.');

        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')),
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('bottom'),
            0,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::restoreBlock
     */
    public function testRestoreBlock(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        // Move block so we can make sure position is kept while restoring the block.

        $zone = $this->layoutService->loadLayoutDraft($block->getLayoutId())->getZone('left');
        $movedBlock = $this->blockService->moveBlockToZone($block, $zone, 1);
        $movedPersistenceBlock = $this->blockHandler->loadBlock($movedBlock->getId(), $movedBlock->getStatus());

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

        self::assertInstanceOf(Collection::class, $collections['default']);
        self::assertInstanceOf(Collection::class, $collections['featured']);

        self::assertSame('45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a', $collections['default']->getId()->toString());
        self::assertSame('da050624-8ae0-5fb9-ae85-092bf8242b89', $collections['featured']->getId()->toString());

        $restoredPersistenceBlock = $this->blockHandler->loadBlock($restoredBlock->getId(), $restoredBlock->getStatus());

        // Make sure the position is not moved.

        self::assertSame($movedPersistenceBlock->layoutId, $restoredPersistenceBlock->layoutId);
        self::assertSame($movedPersistenceBlock->depth, $restoredPersistenceBlock->depth);
        self::assertSame($movedPersistenceBlock->parentId, $restoredPersistenceBlock->parentId);
        self::assertSame($movedPersistenceBlock->placeholder, $restoredPersistenceBlock->placeholder);
        self::assertSame($movedPersistenceBlock->position, $restoredPersistenceBlock->position);
        self::assertSame($movedPersistenceBlock->path, $restoredPersistenceBlock->path);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::restoreBlock
     */
    public function testRestoreBlockRestoresMissingTranslations(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
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
     * @covers \Netgen\Layouts\Core\Service\BlockService::restoreBlock
     */
    public function testRestoreBlockThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Only draft blocks can be restored.');

        $block = $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        $this->blockService->restoreBlock($block);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::enableTranslations
     */
    public function testEnableTranslations(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('129f51de-a535-5094-8517-45d672e06302'));

        $updatedBlock = $this->blockService->enableTranslations($block);

        $layout = $this->layoutService->loadLayoutDraft($block->getLayoutId());
        foreach ($layout->getAvailableLocales() as $locale) {
            self::assertContains($locale, $updatedBlock->getAvailableLocales());
        }

        self::assertTrue($updatedBlock->isTranslatable());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::enableTranslations
     */
    public function testEnableTranslationsThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. You can only enable translations for draft blocks.');

        $block = $this->blockService->loadBlock(Uuid::fromString('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e'));

        $this->blockService->enableTranslations($block);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::enableTranslations
     */
    public function testEnableTranslationsThrowsBadStateExceptionWithEnabledTranslations(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Block is already translatable.');

        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        $this->blockService->enableTranslations($block);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::enableTranslations
     */
    public function testEnableTranslationsThrowsBadStateExceptionWithNonTranslatableParentBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('You can only enable translations if parent block is also translatable.');

        $parentBlock = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));
        $this->blockService->disableTranslations($parentBlock);

        $block = $this->blockService->loadBlockDraft(Uuid::fromString('129f51de-a535-5094-8517-45d672e06302'));

        $this->blockService->enableTranslations($block);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::disableTranslations
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalDisableTranslations
     */
    public function testDisableTranslations(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        $updatedBlock = $this->blockService->disableTranslations($block);

        self::assertFalse($updatedBlock->isTranslatable());

        self::assertNotContains('hr', $updatedBlock->getAvailableLocales());
        self::assertContains('en', $updatedBlock->getAvailableLocales());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::disableTranslations
     * @covers \Netgen\Layouts\Core\Service\BlockService::internalDisableTranslations
     */
    public function testDisableTranslationsOnContainer(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'));
        $childBlock = $this->blockService->loadBlockDraft(Uuid::fromString('129f51de-a535-5094-8517-45d672e06302'));

        $this->blockService->enableTranslations($childBlock);
        $block = $this->blockService->disableTranslations($block);

        self::assertFalse($block->isTranslatable());

        self::assertNotContains('hr', $block->getAvailableLocales());
        self::assertContains('en', $block->getAvailableLocales());

        $childBlock = $this->blockService->loadBlockDraft(Uuid::fromString('129f51de-a535-5094-8517-45d672e06302'));

        self::assertFalse($childBlock->isTranslatable());

        self::assertNotContains('hr', $childBlock->getAvailableLocales());
        self::assertContains('en', $childBlock->getAvailableLocales());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::disableTranslations
     */
    public function testDisableTranslationsThrowsBadStateExceptionWithNonDraftBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. You can only disable translations for draft blocks.');

        $block = $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));

        $this->blockService->disableTranslations($block);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::disableTranslations
     */
    public function testDisableTranslationsThrowsBadStateExceptionWithDisabledTranslations(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Block is not translatable.');

        $block = $this->blockService->loadBlockDraft(Uuid::fromString('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e'));

        $this->blockService->disableTranslations($block);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::deleteBlock
     */
    public function testDeleteBlock(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find block with identifier "28df256a-2467-5527-b398-9269ccc652de"');

        $block = $this->blockService->loadBlockDraft(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));
        $this->blockService->deleteBlock($block);

        $this->blockService->loadBlockDraft($block->getId());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::deleteBlock
     */
    public function testDeleteThrowsBadStateExceptionBlockWithNonDraftBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Only draft blocks can be deleted.');

        $block = $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'));
        $this->blockService->deleteBlock($block);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct(): void
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition('title');

        $struct = $this->blockService->newBlockCreateStruct($blockDefinition);

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'collectionCreateStructs' => [],
                'configStructs' => [],
                'definition' => $blockDefinition,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'name' => '',
                'parameterValues' => [
                    'css_class' => 'some-class',
                    'css_id' => null,
                ],
                'viewType' => 'small',
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStruct(): void
    {
        $struct = $this->blockService->newBlockUpdateStruct('en');

        self::assertSame(
            [
                'alwaysAvailable' => null,
                'configStructs' => [],
                'itemViewType' => null,
                'locale' => 'en',
                'name' => null,
                'parameterValues' => [],
                'viewType' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\BlockService::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStructFromBlock(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('b40aa688-b8e8-5e07-bf82-4a97e5ed8bad'));
        $struct = $this->blockService->newBlockUpdateStruct('en', $block);

        self::assertArrayHasKey('key', $struct->getConfigStructs());

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'configStructs' => [
                    'key' => [
                        'parameterValues' => [
                            'param1' => null,
                            'param2' => null,
                        ],
                    ],
                ],
                'itemViewType' => 'standard',
                'locale' => 'en',
                'name' => 'My sixth block',
                'parameterValues' => [
                    'css_class' => 'CSS class',
                    'css_id' => null,
                ],
                'viewType' => 'title',
            ],
            $this->exportObject($struct, true),
        );
    }
}
