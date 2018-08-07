<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockTranslationUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class BlockHandlerTest extends TestCase
{
    use TestCaseTrait;
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface
     */
    private $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
     */
    private $collectionHandler;

    public function setUp(): void
    {
        $this->createDatabase();

        $this->blockHandler = $this->createBlockHandler();
        $this->layoutHandler = $this->createLayoutHandler();
        $this->collectionHandler = $this->createCollectionHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::getBlockSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockData
     */
    public function testLoadBlock(): void
    {
        $block = $this->blockHandler->loadBlock(31, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/31/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 3,
                    ],
                    'hr' => [
                        'number_of_columns' => 3,
                    ],
                ],
                'config' => [],
                'viewType' => 'grid',
                'itemViewType' => 'standard_with_intro',
                'name' => 'My published block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($block)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "999999"
     */
    public function testLoadBlockThrowsNotFoundException(): void
    {
        $this->blockHandler->loadBlock(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::blockExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::blockExists
     */
    public function testBlockExists(): void
    {
        self::assertTrue($this->blockHandler->blockExists(31, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::blockExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::blockExists
     */
    public function testBlockNotExists(): void
    {
        self::assertFalse($this->blockHandler->blockExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::blockExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::blockExists
     */
    public function testBlockNotExistsInStatus(): void
    {
        self::assertFalse($this->blockHandler->blockExists(36, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadLayoutBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadLayoutBlocksData
     */
    public function testLoadLayoutBlocks(): void
    {
        $blocks = $this->blockHandler->loadLayoutBlocks(
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );

        self::assertCount(7, $blocks);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadZoneBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadZoneBlocksData
     */
    public function testLoadZoneBlocks(): void
    {
        $blocks = $this->blockHandler->loadZoneBlocks(
            $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'right')
        );

        self::assertCount(3, $blocks);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadChildBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadChildBlocksData
     */
    public function testLoadChildBlocks(): void
    {
        $blocks = $this->blockHandler->loadChildBlocks(
            $this->blockHandler->loadBlock(3, Value::STATUS_PUBLISHED)
        );

        self::assertContainsOnlyInstancesOf(Block::class, $blocks);

        self::assertSame(
            [
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                        'hr' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                    'viewType' => 'grid',
                    'itemViewType' => 'standard_with_intro',
                    'name' => 'My published block',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 35,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/35/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 1,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                    'viewType' => 'grid',
                    'itemViewType' => 'standard',
                    'name' => 'My fourth block',
                    'isTranslatable' => false,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList($blocks)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadChildBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadChildBlocksData
     */
    public function testLoadChildBlocksInPlaceholder(): void
    {
        $blocks = $this->blockHandler->loadChildBlocks(
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            'left'
        );

        self::assertContainsOnlyInstancesOf(Block::class, $blocks);

        self::assertSame(
            [
                [
                    'id' => 37,
                    'layoutId' => 2,
                    'depth' => 2,
                    'path' => '/7/33/37/',
                    'parentId' => 33,
                    'placeholder' => 'left',
                    'position' => 0,
                    'definitionIdentifier' => 'text',
                    'parameters' => [
                        'en' => [
                            'content' => 'Text',
                        ],
                    ],
                    'config' => [],
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My seventh block',
                    'isTranslatable' => false,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_DRAFT,
                ],
            ],
            $this->exportObjectList($blocks)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadChildBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadChildBlocksData
     */
    public function testLoadChildBlocksWithUnknownPlaceholder(): void
    {
        self::assertEmpty(
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
                'unknown'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadCollectionReference
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadCollectionReferencesData
     */
    public function testLoadCollectionReference(): void
    {
        $reference = $this->blockHandler->loadCollectionReference(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'default'
        );

        self::assertSame(
            [
                'blockId' => 31,
                'blockStatus' => Value::STATUS_DRAFT,
                'collectionId' => 1,
                'collectionStatus' => Value::STATUS_DRAFT,
                'identifier' => 'default',
            ],
            $this->exportObject($reference)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadCollectionReference
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadCollectionReferencesData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection reference with identifier "non_existing"
     */
    public function testLoadCollectionReferenceThrowsNotFoundException(): void
    {
        $this->blockHandler->loadCollectionReference(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadCollectionReferences
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadCollectionReferencesData
     */
    public function testLoadCollectionReferences(): void
    {
        $references = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT)
        );

        self::assertContainsOnlyInstancesOf(CollectionReference::class, $references);

        self::assertSame(
            [
                [
                    'blockId' => 31,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 1,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'default',
                ],
                [
                    'blockId' => 31,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 3,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'featured',
                ],
            ],
            $this->exportObjectList($references)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCreateBlock(): void
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = true;
        $blockCreateStruct->alwaysAvailable = true;
        $blockCreateStruct->status = Value::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 0;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $blockCreateStruct->config = [
            'config_param' => 'Config value',
        ];

        $createdBlock = $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root'
        );

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/39/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'new_block',
                'parameters' => [
                    'en' => [
                        'a_param' => 'A value',
                    ],
                    'hr' => [
                        'a_param' => 'A value',
                    ],
                ],
                'config' => [
                    'config_param' => 'Config value',
                ],
                'viewType' => 'large',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($createdBlock)
        );

        $secondBlock = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        self::assertSame(1, $secondBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCreateBlockTranslation(): void
    {
        $block = $this->blockHandler->createBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'de',
            'en'
        );

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/31/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                    'de' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($block)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCreateBlockTranslationWithNonMainSourceLocale(): void
    {
        $block = $this->blockHandler->createBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'de',
            'hr'
        );

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/31/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                    'de' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($block)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Block already has the provided locale.
     */
    public function testCreateBlockTranslationThrowsBadStateExceptionWithExistingLocale(): void
    {
        $this->blockHandler->createBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'en',
            'hr'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Block does not have the provided source locale.
     */
    public function testCreateBlockTranslationThrowsBadStateExceptionWithNonExistingSourceLocale(): void
    {
        $this->blockHandler->createBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'de',
            'fr'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     */
    public function testCreateBlockWithNoParent(): void
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = false;
        $blockCreateStruct->alwaysAvailable = true;
        $blockCreateStruct->status = Value::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 0;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $blockCreateStruct->config = [
            'config_param' => 'Config value',
        ];

        $block = $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT)
        );

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => 1,
                'depth' => 0,
                'path' => '/39/',
                'parentId' => null,
                'placeholder' => null,
                'position' => null,
                'definitionIdentifier' => 'new_block',
                'parameters' => [
                    'en' => [
                        'a_param' => 'A value',
                    ],
                ],
                'config' => [
                    'config_param' => 'Config value',
                ],
                'viewType' => 'large',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($block)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     */
    public function testCreateBlockWithNoPosition(): void
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = true;
        $blockCreateStruct->alwaysAvailable = true;
        $blockCreateStruct->status = Value::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $blockCreateStruct->config = [
            'config' => 'Config value',
        ];

        $block = $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root'
        );

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/39/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 2,
                'definitionIdentifier' => 'new_block',
                'parameters' => [
                    'en' => [
                        'a_param' => 'A value',
                    ],
                    'hr' => [
                        'a_param' => 'A value',
                    ],
                ],
                'config' => [
                    'config' => 'Config value',
                ],
                'viewType' => 'large',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($block)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Target block is not in the provided layout.
     */
    public function testCreateBlockThrowsBadStateExceptionOnTargetBlockInDifferentLayout(): void
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = true;
        $blockCreateStruct->alwaysAvailable = true;
        $blockCreateStruct->status = Value::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 0;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(5, Value::STATUS_DRAFT),
            'root'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
     */
    public function testCreateBlockThrowsBadStateExceptionOnNegativePosition(): void
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = true;
        $blockCreateStruct->alwaysAvailable = true;
        $blockCreateStruct->status = Value::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = -5;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCreateBlockThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = true;
        $blockCreateStruct->alwaysAvailable = true;
        $blockCreateStruct->status = Value::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 9999;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createCollectionReference
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createCollectionReference
     */
    public function testCreateCollectionReference(): void
    {
        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $collection = $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);

        $reference = $this->blockHandler->createCollectionReference(
            $block,
            $collection,
            'new'
        );

        self::assertSame(
            [
                'blockId' => $block->id,
                'blockStatus' => $block->status,
                'collectionId' => $collection->id,
                'collectionStatus' => $collection->status,
                'identifier' => 'new',
            ],
            $this->exportObject($reference)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlock
     */
    public function testUpdateBlock(): void
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->viewType = 'large';
        $blockUpdateStruct->itemViewType = 'new';
        $blockUpdateStruct->name = 'Updated name';
        $blockUpdateStruct->config = ['config'];
        $blockUpdateStruct->isTranslatable = false;
        $blockUpdateStruct->alwaysAvailable = false;
        $blockUpdateStruct->config = ['config'];

        $updatedBlock = $this->blockHandler->updateBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $blockUpdateStruct
        );

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/31/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => ['config'],
                'viewType' => 'large',
                'itemViewType' => 'new',
                'name' => 'Updated name',
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => false,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($updatedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlock
     */
    public function testUpdateBlockWithDefaultValues(): void
    {
        $blockUpdateStruct = new BlockUpdateStruct();

        $updatedBlock = $this->blockHandler->updateBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $blockUpdateStruct
        );

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/31/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($updatedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlockTranslation
     */
    public function testUpdateBlockTranslation(): void
    {
        $translationUpdateStruct = new BlockTranslationUpdateStruct();

        $translationUpdateStruct->parameters = [
            'number_of_columns' => 4,
            'some_param' => 'Some value',
        ];

        $updatedBlock = $this->blockHandler->updateBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'en',
            $translationUpdateStruct
        );

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/31/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 4,
                        'some_param' => 'Some value',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($updatedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlockTranslation
     */
    public function testUpdateBlockTranslationWithDefaultValues(): void
    {
        $translationUpdateStruct = new BlockTranslationUpdateStruct();

        $updatedBlock = $this->blockHandler->updateBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'en',
            $translationUpdateStruct
        );

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/31/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($updatedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlockTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Block does not have the provided locale.
     */
    public function testUpdateBlockTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->blockHandler->updateBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'de',
            new BlockTranslationUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::setMainTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlock
     */
    public function testSetMainTranslation(): void
    {
        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $block = $this->blockHandler->setMainTranslation($block, 'hr');

        self::assertSame('hr', $block->mainLocale);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::setMainTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "mainLocale" has an invalid state. Block does not have the provided locale.
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $this->blockHandler->setMainTranslation($block, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlock(): void
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root'
        );

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/39/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 2,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($copiedBlock)
        );

        self::assertSame(
            [
                [
                    'blockId' => 39,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 7,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'default',
                ],
                [
                    'blockId' => 39,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 8,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'featured',
                ],
            ],
            $this->exportObjectList(
                $this->blockHandler->loadCollectionReferences($copiedBlock)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockWithPosition(): void
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root',
            1
        );

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/39/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 1,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($copiedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockWithSamePosition(): void
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root',
            0
        );

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/39/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($copiedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockWithLastPosition(): void
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root',
            2
        );

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/39/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 2,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($copiedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockWithLowerPosition(): void
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(35, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root',
            0
        );

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/39/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 3,
                    ],
                ],
                'config' => [],
                'viewType' => 'grid',
                'itemViewType' => 'standard',
                'name' => 'My fourth block',
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($copiedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
     */
    public function testCopyBlockThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root',
            -1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCopyBlockThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root',
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockWithChildBlocks(): void
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(7, Value::STATUS_DRAFT),
            'root'
        );

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => 2,
                'depth' => 1,
                'path' => '/7/39/',
                'parentId' => 7,
                'placeholder' => 'root',
                'position' => 3,
                'definitionIdentifier' => 'two_columns',
                'parameters' => [
                    'en' => [],
                ],
                'config' => [],
                'viewType' => 'two_columns_50_50',
                'itemViewType' => 'standard',
                'name' => 'My third block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($copiedBlock)
        );

        $copiedSubBlock = $this->blockHandler->loadBlock(40, Value::STATUS_DRAFT);

        self::assertSame(
            [
                'id' => 40,
                'layoutId' => 2,
                'depth' => 2,
                'path' => '/7/39/40/',
                'parentId' => 39,
                'placeholder' => 'left',
                'position' => 0,
                'definitionIdentifier' => 'text',
                'parameters' => [
                    'en' => [
                        'content' => 'Text',
                    ],
                ],
                'config' => [],
                'viewType' => 'text',
                'itemViewType' => 'standard',
                'name' => 'My seventh block',
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($copiedSubBlock)
        );

        self::assertSame(
            [
                [
                    'blockId' => 40,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 7,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'default',
                ],
            ],
            $this->exportObjectList(
                $this->blockHandler->loadCollectionReferences($copiedSubBlock)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockToBlockInDifferentLayout(): void
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(8, Value::STATUS_DRAFT),
            'root'
        );

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => 2,
                'depth' => 1,
                'path' => '/8/39/',
                'parentId' => 8,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($copiedBlock)
        );

        self::assertSame(
            [
                [
                    'blockId' => 39,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 7,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'default',
                ],
                [
                    'blockId' => 39,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 8,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'featured',
                ],
            ],
            $this->exportObjectList($this->blockHandler->loadCollectionReferences($copiedBlock))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Block cannot be copied below itself or its children.
     */
    public function testCopyBlockBelowSelf(): void
    {
        $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Block cannot be copied below itself or its children.
     */
    public function testCopyBlockBelowChildren(): void
    {
        $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(37, Value::STATUS_DRAFT),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     */
    public function testMoveBlock(): void
    {
        $movedBlock = $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(4, Value::STATUS_DRAFT),
            'root',
            0
        );

        self::assertSame(
            [
                'id' => 33,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/4/33/',
                'parentId' => 4,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'two_columns',
                'parameters' => [
                    'en' => [],
                ],
                'config' => [],
                'viewType' => 'two_columns_50_50',
                'itemViewType' => 'standard',
                'name' => 'My third block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($movedBlock)
        );

        self::assertSame(
            [
                'id' => 37,
                'layoutId' => 1,
                'depth' => 2,
                'path' => '/4/33/37/',
                'parentId' => 33,
                'placeholder' => 'left',
                'position' => 0,
                'definitionIdentifier' => 'text',
                'parameters' => [
                    'en' => [
                        'content' => 'Text',
                    ],
                ],
                'config' => [],
                'viewType' => 'text',
                'itemViewType' => 'standard',
                'name' => 'My seventh block',
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($this->blockHandler->loadBlock(37, Value::STATUS_DRAFT))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Block is already in specified target block and placeholder.
     */
    public function testMoveBlockThrowsBadStateExceptionOnMovingToSamePlace(): void
    {
        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(7, Value::STATUS_DRAFT),
            'root',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Block cannot be moved below itself or its children.
     */
    public function testMoveBlockThrowsBadStateExceptionOnMovingToSelf(): void
    {
        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            'main',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Block cannot be moved below itself or its children.
     */
    public function testMoveBlockThrowsBadStateExceptionOnMovingToChildren(): void
    {
        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(37, Value::STATUS_DRAFT),
            'main',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
     */
    public function testMoveBlockThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(4, Value::STATUS_DRAFT),
            'root',
            -1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testMoveBlockThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(4, Value::STATUS_DRAFT),
            'root',
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     */
    public function testMoveBlockToPosition(): void
    {
        $movedBlock = $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            1
        );

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/31/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 1,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($movedBlock)
        );

        $firstBlock = $this->blockHandler->loadBlock(32, Value::STATUS_DRAFT);
        self::assertSame(0, $firstBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     */
    public function testMoveBlockToLowerPosition(): void
    {
        $movedBlock = $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(35, Value::STATUS_DRAFT),
            0
        );

        self::assertSame(
            [
                'id' => 35,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/35/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 3,
                    ],
                ],
                'config' => [],
                'viewType' => 'grid',
                'itemViewType' => 'standard',
                'name' => 'My fourth block',
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($movedBlock)
        );

        $firstBlock = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        self::assertSame(1, $firstBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Root blocks cannot be moved.
     */
    public function testMoveBlockThrowsBadStateExceptionOnMovingRootBlock(): void
    {
        $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(1, Value::STATUS_DRAFT),
            1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
     */
    public function testMoveBlockToPositionThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            -1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testMoveBlockToPositionThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockCollectionsStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     */
    public function testCreateBlockStatus(): void
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT)
        );

        $block = $this->blockHandler->createBlockStatus(
            $this->blockHandler->loadBlock(31, Value::STATUS_PUBLISHED),
            Value::STATUS_DRAFT
        );

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/31/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 3,
                    ],
                    'hr' => [
                        'number_of_columns' => 3,
                    ],
                ],
                'config' => [],
                'viewType' => 'grid',
                'itemViewType' => 'standard_with_intro',
                'name' => 'My published block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($block)
        );

        $collectionReferences = $this->blockHandler->loadCollectionReferences($block);

        self::assertCount(2, $collectionReferences);

        $collectionIds = [
            $collectionReferences[0]->collectionId,
            $collectionReferences[1]->collectionId,
        ];

        self::assertContains(2, $collectionIds);
        self::assertContains(3, $collectionIds);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::restoreBlock
     */
    public function testRestoreBlock(): void
    {
        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);

        $movedBlock = $this->blockHandler->moveBlock(
            $block,
            $this->blockHandler->loadBlock(2, Value::STATUS_DRAFT),
            'root',
            1
        );

        $restoredBlock = $this->blockHandler->restoreBlock($movedBlock, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/2/31/',
                'parentId' => 2,
                'placeholder' => 'root',
                'position' => 1,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 3,
                    ],
                    'hr' => [
                        'number_of_columns' => 3,
                    ],
                ],
                'config' => [],
                'viewType' => 'grid',
                'itemViewType' => 'standard_with_intro',
                'name' => 'My published block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($restoredBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::restoreBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Block is already in provided status.
     */
    public function testRestoreBlockThrowsBadStateExceptionWithSameState(): void
    {
        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);

        $this->blockHandler->restoreBlock($block, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadSubBlockIds
     */
    public function testDeleteBlock(): void
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT)
        );

        $secondBlock = $this->blockHandler->loadBlock(32, Value::STATUS_DRAFT);
        self::assertSame(0, $secondBlock->position);

        try {
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
            self::fail('Block still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        try {
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
            self::fail('Collection still exists after deleting a block.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that shared collection still exists
        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlocks
     * @doesNotPerformAssertions
     */
    public function testDeleteBlockWithSubBlocks(): void
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT)
        );

        try {
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT);
            self::fail('Block still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(37, Value::STATUS_DRAFT);
            self::fail('Sub-block still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        try {
            $this->collectionHandler->loadCollection(6, Value::STATUS_DRAFT);
            self::fail('Collection still exists after deleting a sub-block.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlockTranslations
     */
    public function testDeleteBlockTranslation(): void
    {
        $block = $this->blockHandler->deleteBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'hr'
        );

        self::assertSame(
            [
                'id' => 31,
                'layoutId' => 1,
                'depth' => 1,
                'path' => '/3/31/',
                'parentId' => 3,
                'placeholder' => 'root',
                'position' => 0,
                'definitionIdentifier' => 'list',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                ],
                'config' => [],
                'viewType' => 'list',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($block)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlockTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Block does not have the provided locale.
     */
    public function testDeleteBlockTranslationWithNonExistingLocale(): void
    {
        $this->blockHandler->deleteBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'de'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlockTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Main translation cannot be removed from the block.
     */
    public function testDeleteBlockTranslationWithMainLocale(): void
    {
        $this->blockHandler->deleteBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'en'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteLayoutBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteCollectionReferences
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockCollectionIds
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadLayoutBlockIds
     */
    public function testDeleteLayoutBlocks(): void
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);

        // First we need to delete all zones to correctly delete the blocks
        $query = $this->databaseConnection->createQueryBuilder();

        $query->delete('ngbm_zone')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('layout_id', $layout->id, Type::INTEGER)
            ->setParameter('status', $layout->status, Type::INTEGER);

        $query->execute();

        $this->blockHandler->deleteLayoutBlocks($layout->id, $layout->status);

        self::assertEmpty($this->blockHandler->loadLayoutBlocks($layout));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteCollectionReferences
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockCollectionIds
     * @doesNotPerformAssertions
     */
    public function testDeleteBlocks(): void
    {
        $this->blockHandler->deleteBlocks([31, 32]);

        try {
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
            self::fail(
                sprintf(
                    'Draft block %d still available after deleting',
                    31
                )
            );
        } catch (NotFoundException $e) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(32, Value::STATUS_DRAFT);
            self::fail(
                sprintf(
                    'Draft block %d still available after deleting',
                    32
                )
            );
        } catch (NotFoundException $e) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(31, Value::STATUS_PUBLISHED);
            self::fail(
                sprintf(
                    'Published block %d still available after deleting',
                    31
                )
            );
        } catch (NotFoundException $e) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(32, Value::STATUS_PUBLISHED);
            self::fail(
                sprintf(
                    'Published block %d still available after deleting',
                    32
                )
            );
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteCollectionReferences
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockCollectionIds
     */
    public function testDeleteBlocksInStatus(): void
    {
        $this->blockHandler->deleteBlocks([31, 32], Value::STATUS_PUBLISHED);

        $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $this->blockHandler->loadBlock(32, Value::STATUS_DRAFT);

        try {
            $this->blockHandler->loadBlock(31, Value::STATUS_PUBLISHED);
            self::fail(
                sprintf(
                    'Published block %d still available after deleting',
                    31
                )
            );
        } catch (NotFoundException $e) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(32, Value::STATUS_PUBLISHED);
            self::fail(
                sprintf(
                    'Published block %d still available after deleting',
                    32
                )
            );
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Fake assertion to disable risky warning
        self::assertTrue(true);
    }
}
