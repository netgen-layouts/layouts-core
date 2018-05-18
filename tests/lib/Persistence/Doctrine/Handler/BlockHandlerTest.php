<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\TranslationUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\TestCase;

final class BlockHandlerTest extends TestCase
{
    use TestCaseTrait;

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

    public function setUp()
    {
        $this->createDatabase();

        $this->blockHandler = $this->createBlockHandler();
        $this->layoutHandler = $this->createLayoutHandler();
        $this->collectionHandler = $this->createCollectionHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
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
    public function testLoadBlock()
    {
        $this->assertEquals(
            new Block(
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
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            $this->blockHandler->loadBlock(31, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "999999"
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $this->blockHandler->loadBlock(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::blockExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::blockExists
     */
    public function testBlockExists()
    {
        $this->assertTrue($this->blockHandler->blockExists(31, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::blockExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::blockExists
     */
    public function testBlockNotExists()
    {
        $this->assertFalse($this->blockHandler->blockExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::blockExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::blockExists
     */
    public function testBlockNotExistsInStatus()
    {
        $this->assertFalse($this->blockHandler->blockExists(36, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadLayoutBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadLayoutBlocksData
     */
    public function testLoadLayoutBlocks()
    {
        $blocks = $this->blockHandler->loadLayoutBlocks(
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(7, $blocks);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadZoneBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadZoneBlocksData
     */
    public function testLoadZoneBlocks()
    {
        $blocks = $this->blockHandler->loadZoneBlocks(
            $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'right')
        );

        $this->assertCount(3, $blocks);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadChildBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadChildBlocksData
     */
    public function testLoadChildBlocks()
    {
        $this->assertEquals(
            [
                new Block(
                    [
                        'id' => 31,
                        'layoutId' => 1,
                        'depth' => 1,
                        'path' => '/3/31/',
                        'parentId' => 3,
                        'placeholder' => 'root',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'grid',
                        'itemViewType' => 'standard_with_intro',
                        'name' => 'My published block',
                        'isTranslatable' => true,
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en', 'hr'],
                        'mainLocale' => 'en',
                        'status' => Value::STATUS_PUBLISHED,
                        'parameters' => [
                            'en' => [
                                'number_of_columns' => 3,
                            ],
                            'hr' => [
                                'number_of_columns' => 3,
                            ],
                        ],
                        'config' => [],
                    ]
                ),
                new Block(
                    [
                        'id' => 35,
                        'layoutId' => 1,
                        'depth' => 1,
                        'path' => '/3/35/',
                        'parentId' => 3,
                        'placeholder' => 'root',
                        'position' => 1,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My fourth block',
                        'isTranslatable' => false,
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en'],
                        'mainLocale' => 'en',
                        'status' => Value::STATUS_PUBLISHED,
                        'parameters' => [
                            'en' => [
                                'number_of_columns' => 3,
                            ],
                        ],
                        'config' => [],
                    ]
                ),
            ],
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(3, Value::STATUS_PUBLISHED)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadChildBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadChildBlocksData
     */
    public function testLoadChildBlocksInPlaceholder()
    {
        $this->assertEquals(
            [
                new Block(
                    [
                        'id' => 37,
                        'layoutId' => 2,
                        'depth' => 2,
                        'path' => '/7/33/37/',
                        'parentId' => 33,
                        'placeholder' => 'left',
                        'position' => 0,
                        'definitionIdentifier' => 'text',
                        'viewType' => 'text',
                        'itemViewType' => 'standard',
                        'name' => 'My seventh block',
                        'isTranslatable' => false,
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en'],
                        'mainLocale' => 'en',
                        'status' => Value::STATUS_DRAFT,
                        'parameters' => [
                            'en' => [
                                'content' => 'Text',
                            ],
                        ],
                        'config' => [],
                    ]
                ),
            ],
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
                'left'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadChildBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadChildBlocksData
     */
    public function testLoadChildBlocksWithUnknownPlaceholder()
    {
        $this->assertEmpty(
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
    public function testLoadCollectionReference()
    {
        $this->assertEquals(
            new CollectionReference(
                [
                    'blockId' => 31,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 1,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'default',
                ]
            ),
            $this->blockHandler->loadCollectionReference(
                $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
                'default'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadCollectionReference
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadCollectionReferencesData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection reference with identifier "non_existing"
     */
    public function testLoadCollectionReferenceThrowsNotFoundException()
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
    public function testLoadCollectionReferences()
    {
        $this->assertEquals(
            [
                new CollectionReference(
                    [
                        'blockId' => 31,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 1,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'default',
                    ]
                ),
                new CollectionReference(
                    [
                        'blockId' => 31,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 3,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'featured',
                    ]
                ),
            ],
            $this->blockHandler->loadCollectionReferences(
                $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCreateBlock()
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

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/39/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'new_block',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $this->blockHandler->createBlock(
                $blockCreateStruct,
                $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
                $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
                'root'
            )
        );

        $secondBlock = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $this->assertEquals(1, $secondBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCreateBlockTranslation()
    {
        $block = $this->blockHandler->createBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'de',
            'en'
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr', 'de'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $block
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCreateBlockTranslationWithNonMainSourceLocale()
    {
        $block = $this->blockHandler->createBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'de',
            'hr'
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr', 'de'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $block
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Block already has the provided locale.
     */
    public function testCreateBlockTranslationThrowsBadStateExceptionWithExistingLocale()
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
    public function testCreateBlockTranslationThrowsBadStateExceptionWithNonExistingSourceLocale()
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
    public function testCreateBlockWithNoParent()
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

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => 1,
                    'depth' => 0,
                    'path' => '/39/',
                    'parentId' => null,
                    'placeholder' => null,
                    'position' => null,
                    'definitionIdentifier' => 'new_block',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => [
                        'en' => [
                            'a_param' => 'A value',
                        ],
                    ],
                    'config' => [
                        'config_param' => 'Config value',
                    ],
                ]
            ),
            $this->blockHandler->createBlock(
                $blockCreateStruct,
                $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     */
    public function testCreateBlockWithNoPosition()
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

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/39/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 2,
                    'definitionIdentifier' => 'new_block',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $this->blockHandler->createBlock(
                $blockCreateStruct,
                $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
                $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
                'root'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Target block is not in the provided layout.
     */
    public function testCreateBlockThrowsBadStateExceptionOnTargetBlockInDifferentLayout()
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
    public function testCreateBlockThrowsBadStateExceptionOnNegativePosition()
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
    public function testCreateBlockThrowsBadStateExceptionOnTooLargePosition()
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
    public function testCreateCollectionReference()
    {
        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $collection = $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);

        $reference = $this->blockHandler->createCollectionReference(
            $block,
            new CollectionReferenceCreateStruct(
                [
                    'identifier' => 'new',
                    'collection' => $collection,
                ]
            )
        );

        $this->assertEquals(
            new CollectionReference(
                [
                    'blockId' => $block->id,
                    'blockStatus' => $block->status,
                    'collectionId' => $collection->id,
                    'collectionStatus' => $collection->status,
                    'identifier' => 'new',
                ]
            ),
            $reference
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlock
     */
    public function testUpdateBlock()
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->viewType = 'large';
        $blockUpdateStruct->itemViewType = 'new';
        $blockUpdateStruct->name = 'Updated name';
        $blockUpdateStruct->config = ['config'];
        $blockUpdateStruct->isTranslatable = false;
        $blockUpdateStruct->alwaysAvailable = false;
        $blockUpdateStruct->config = ['config'];

        $this->assertEquals(
            new Block(
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'large',
                    'itemViewType' => 'new',
                    'name' => 'Updated name',
                    'isTranslatable' => false,
                    'alwaysAvailable' => false,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $this->blockHandler->updateBlock(
                $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
                $blockUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlock
     */
    public function testUpdateBlockWithDefaultValues()
    {
        $blockUpdateStruct = new BlockUpdateStruct();

        $this->assertEquals(
            new Block(
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $this->blockHandler->updateBlock(
                $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
                $blockUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlockTranslation
     */
    public function testUpdateBlockTranslation()
    {
        $translationUpdateStruct = new TranslationUpdateStruct();

        $translationUpdateStruct->parameters = [
            'number_of_columns' => 4,
            'some_param' => 'Some value',
        ];

        $this->assertEquals(
            new Block(
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $this->blockHandler->updateBlockTranslation(
                $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
                'en',
                $translationUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlockTranslation
     */
    public function testUpdateBlockTranslationWithDefaultValues()
    {
        $translationUpdateStruct = new TranslationUpdateStruct();

        $this->assertEquals(
            new Block(
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $this->blockHandler->updateBlockTranslation(
                $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
                'en',
                $translationUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlockTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Block does not have the provided locale.
     */
    public function testUpdateBlockTranslationThrowsBadStateExceptionWithNonExistingLocale()
    {
        $this->blockHandler->updateBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'de',
            new TranslationUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::setMainTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlock
     */
    public function testSetMainTranslation()
    {
        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $block = $this->blockHandler->setMainTranslation($block, 'hr');

        $this->assertEquals('hr', $block->mainLocale);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::setMainTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "mainLocale" has an invalid state. Block does not have the provided locale.
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale()
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
    public function testCopyBlock()
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root'
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/39/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 2,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $copiedBlock
        );

        $this->assertEquals(
            [
                new CollectionReference(
                    [
                        'blockId' => 39,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 7,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'default',
                    ]
                ),
                new CollectionReference(
                    [
                        'blockId' => 39,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 8,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'featured',
                    ]
                ),
            ],
            $this->blockHandler->loadCollectionReferences($copiedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockWithPosition()
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root',
            1
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/39/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 1,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $copiedBlock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockWithSamePosition()
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root',
            0
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/39/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $copiedBlock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockWithLastPosition()
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root',
            2
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/39/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 2,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $copiedBlock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockWithLowerPosition()
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(35, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root',
            0
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/39/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'grid',
                    'itemViewType' => 'standard',
                    'name' => 'My fourth block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                ]
            ),
            $copiedBlock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
     */
    public function testCopyBlockThrowsBadStateExceptionOnNegativePosition()
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
    public function testCopyBlockThrowsBadStateExceptionOnTooLargePosition()
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
    public function testCopyBlockWithChildBlocks()
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(7, Value::STATUS_DRAFT),
            'root'
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => 2,
                    'depth' => 1,
                    'path' => '/7/39/',
                    'parentId' => 7,
                    'placeholder' => 'root',
                    'position' => 3,
                    'definitionIdentifier' => 'two_columns',
                    'viewType' => 'two_columns_50_50',
                    'itemViewType' => 'standard',
                    'name' => 'My third block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => [
                        'en' => [],
                    ],
                    'config' => [],
                ]
            ),
            $copiedBlock
        );

        $copiedSubBlock = $this->blockHandler->loadBlock(40, Value::STATUS_DRAFT);

        $this->assertEquals(
            new Block(
                [
                    'id' => 40,
                    'layoutId' => 2,
                    'depth' => 2,
                    'path' => '/7/39/40/',
                    'parentId' => 39,
                    'placeholder' => 'left',
                    'position' => 0,
                    'definitionIdentifier' => 'text',
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My seventh block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => [
                        'en' => [
                            'content' => 'Text',
                        ],
                    ],
                    'config' => [],
                ]
            ),
            $copiedSubBlock
        );

        $this->assertEquals(
            [
                new CollectionReference(
                    [
                        'blockId' => 40,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 7,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'default',
                    ]
                ),
            ],
            $this->blockHandler->loadCollectionReferences($copiedSubBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockTranslation
     */
    public function testCopyBlockToBlockInDifferentLayout()
    {
        $copiedBlock = $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $this->blockHandler->loadBlock(8, Value::STATUS_DRAFT),
            'root'
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => 2,
                    'depth' => 1,
                    'path' => '/8/39/',
                    'parentId' => 8,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $copiedBlock
        );

        $this->assertEquals(
            [
                new CollectionReference(
                    [
                        'blockId' => 39,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 7,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'default',
                    ]
                ),
                new CollectionReference(
                    [
                        'blockId' => 39,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 8,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'featured',
                    ]
                ),
            ],
            $this->blockHandler->loadCollectionReferences($copiedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Block cannot be copied below itself or its children.
     */
    public function testCopyBlockBelowSelf()
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
    public function testCopyBlockBelowChildren()
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
    public function testMoveBlock()
    {
        $this->assertEquals(
            new Block(
                [
                    'id' => 33,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/4/33/',
                    'parentId' => 4,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'two_columns',
                    'viewType' => 'two_columns_50_50',
                    'itemViewType' => 'standard',
                    'name' => 'My third block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => [
                        'en' => [],
                    ],
                    'config' => [],
                ]
            ),
            $this->blockHandler->moveBlock(
                $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
                $this->blockHandler->loadBlock(4, Value::STATUS_DRAFT),
                'root',
                0
            )
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 37,
                    'layoutId' => 1,
                    'depth' => 2,
                    'path' => '/4/33/37/',
                    'parentId' => 33,
                    'placeholder' => 'left',
                    'position' => 0,
                    'definitionIdentifier' => 'text',
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My seventh block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => [
                        'en' => [
                            'content' => 'Text',
                        ],
                    ],
                    'config' => [],
                ]
            ),
            $this->blockHandler->loadBlock(37, Value::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Block is already in specified target block and placeholder.
     */
    public function testMoveBlockThrowsBadStateExceptionOnMovingToSamePlace()
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
    public function testMoveBlockThrowsBadStateExceptionOnMovingToSelf()
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
    public function testMoveBlockThrowsBadStateExceptionOnMovingToChildren()
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
    public function testMoveBlockThrowsBadStateExceptionOnNegativePosition()
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
    public function testMoveBlockThrowsBadStateExceptionOnTooLargePosition()
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
    public function testMoveBlockToPosition()
    {
        $this->assertEquals(
            new Block(
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 1,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
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
                ]
            ),
            $this->blockHandler->moveBlockToPosition(
                $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
                1
            )
        );

        $firstBlock = $this->blockHandler->loadBlock(32, Value::STATUS_DRAFT);
        $this->assertEquals(0, $firstBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     */
    public function testMoveBlockToLowerPosition()
    {
        $this->assertEquals(
            new Block(
                [
                    'id' => 35,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/35/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'grid',
                    'itemViewType' => 'standard',
                    'name' => 'My fourth block',
                    'isTranslatable' => false,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                ]
            ),
            $this->blockHandler->moveBlockToPosition(
                $this->blockHandler->loadBlock(35, Value::STATUS_DRAFT),
                0
            )
        );

        $firstBlock = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $this->assertEquals(1, $firstBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
     */
    public function testMoveBlockToPositionThrowsBadStateExceptionOnNegativePosition()
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
    public function testMoveBlockToPositionThrowsBadStateExceptionOnTooLargePosition()
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
    public function testCreateBlockStatus()
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT)
        );

        $block = $this->blockHandler->createBlockStatus(
            $this->blockHandler->loadBlock(31, Value::STATUS_PUBLISHED),
            Value::STATUS_DRAFT
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'grid',
                    'itemViewType' => 'standard_with_intro',
                    'name' => 'My published block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                        'hr' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                ]
            ),
            $block
        );

        $collectionReferences = $this->blockHandler->loadCollectionReferences($block);

        $this->assertCount(2, $collectionReferences);

        $collectionIds = [
            $collectionReferences[0]->collectionId,
            $collectionReferences[1]->collectionId,
        ];

        $this->assertContains(2, $collectionIds);
        $this->assertContains(3, $collectionIds);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::restoreBlock
     */
    public function testRestoreBlock()
    {
        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);

        $movedBlock = $this->blockHandler->moveBlock(
            $block,
            $this->blockHandler->loadBlock(2, Value::STATUS_DRAFT),
            'root',
            1
        );

        $restoredBlock = $this->blockHandler->restoreBlock($movedBlock, Value::STATUS_PUBLISHED);

        $this->assertEquals(
            new Block(
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/2/31/',
                    'parentId' => 2,
                    'placeholder' => 'root',
                    'position' => 1,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'grid',
                    'itemViewType' => 'standard_with_intro',
                    'status' => Value::STATUS_DRAFT,
                    'name' => 'My published block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en', 'hr'],
                    'mainLocale' => 'en',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                        'hr' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                ]
            ),
            $restoredBlock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::restoreBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Block is already in provided status.
     */
    public function testRestoreBlockThrowsBadStateExceptionWithSameState()
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
    public function testDeleteBlock()
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT)
        );

        $secondBlock = $this->blockHandler->loadBlock(32, Value::STATUS_DRAFT);
        $this->assertEquals(0, $secondBlock->position);

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
    public function testDeleteBlockWithSubBlocks()
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
    public function testDeleteBlockTranslation()
    {
        $block = $this->blockHandler->deleteBlockTranslation(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            'hr'
        );

        $this->assertEquals(
            new Block(
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en'],
                    'mainLocale' => 'en',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 2,
                            'css_class' => 'css-class',
                            'css_id' => 'css-id',
                        ],
                    ],
                    'config' => [],
                ]
            ),
            $block
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlockTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlockTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Block does not have the provided locale.
     */
    public function testDeleteBlockTranslationWithNonExistingLocale()
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
    public function testDeleteBlockTranslationWithMainLocale()
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
    public function testDeleteLayoutBlocks()
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

        $this->assertEmpty($this->blockHandler->loadLayoutBlocks($layout));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteCollectionReferences
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockCollectionIds
     * @doesNotPerformAssertions
     */
    public function testDeleteBlocks()
    {
        $this->blockHandler->deleteBlocks([31, 32]);

        try {
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
            $this->fail(
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
            $this->fail(
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
            $this->fail(
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
            $this->fail(
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
    public function testDeleteBlocksInStatus()
    {
        $this->blockHandler->deleteBlocks([31, 32], Value::STATUS_PUBLISHED);

        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $this->assertInstanceOf(Block::class, $block);

        $block = $this->blockHandler->loadBlock(32, Value::STATUS_DRAFT);
        $this->assertInstanceOf(Block::class, $block);

        try {
            $this->blockHandler->loadBlock(31, Value::STATUS_PUBLISHED);
            $this->fail(
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
            $this->fail(
                sprintf(
                    'Published block %d still available after deleting',
                    32
                )
            );
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
