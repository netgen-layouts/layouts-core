<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\Persistence\Values\Page\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Page\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReferenceCreateStruct;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReferenceUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\TestCase;

class BlockHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler
     */
    protected $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * Sets up the tests.
     */
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::getBlockSelectQuery
     */
    public function testLoadBlock()
    {
        $this->assertEquals(
            new Block(
                array(
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'placeholderParameters' => array(),
                    'parameters' => array(
                        'number_of_columns' => 3,
                    ),
                    'viewType' => 'grid',
                    'itemViewType' => 'standard_with_intro',
                    'name' => 'My published block',
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->blockHandler->loadBlock(31, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadChildBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadChildBlocksData
     */
    public function testLoadChildBlocks()
    {
        $this->assertEquals(
            array(
                new Block(
                    array(
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
                        'status' => Value::STATUS_PUBLISHED,
                        'placeholderParameters' => array(),
                        'parameters' => array(
                            'number_of_columns' => 3,
                        ),
                    )
                ),
                new Block(
                    array(
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
                        'status' => Value::STATUS_PUBLISHED,
                        'placeholderParameters' => array(),
                        'parameters' => array(
                            'number_of_columns' => 3,
                        ),
                    )
                ),
            ),
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
            array(
                new Block(
                    array(
                        'id' => 37,
                        'layoutId' => 2,
                        'depth' => 2,
                        'path' => '/7/33/37/',
                        'parentId' => 33,
                        'placeholder' => 'main',
                        'position' => 0,
                        'definitionIdentifier' => 'text',
                        'viewType' => 'text',
                        'itemViewType' => 'standard',
                        'name' => 'My seventh block',
                        'status' => Value::STATUS_DRAFT,
                        'placeholderParameters' => array(),
                        'parameters' => array(
                            'content' => 'Text',
                        ),
                    )
                ),
            ),
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT),
                'main'
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
                array(
                    'blockId' => 31,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 1,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'default',
                    'offset' => 0,
                    'limit' => null,
                )
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
            array(
                new CollectionReference(
                    array(
                        'blockId' => 31,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 1,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'default',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
                new CollectionReference(
                    array(
                        'blockId' => 31,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 3,
                        'collectionStatus' => Value::STATUS_PUBLISHED,
                        'identifier' => 'featured',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
            ),
            $this->blockHandler->loadCollectionReferences(
                $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCreateBlock()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->layoutId = 1;
        $blockCreateStruct->status = VAlue::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 0;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->placeholderParameters = array(
            'left' => array(
                'param' => 'Value',
            ),
        );

        $blockCreateStruct->parameters = array(
            'a_param' => 'A value',
        );

        $this->assertEquals(
            new Block(
                array(
                    'id' => 38,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/38/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'new_block',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'placeholderParameters' => array(
                        'left' => array(
                            'param' => 'Value',
                        ),
                    ),
                )
            ),
            $this->blockHandler->createBlock(
                $blockCreateStruct,
                $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
                'root'
            )
        );

        $secondBlock = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
        $this->assertEquals(1, $secondBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCreateBlockWithNoParent()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->layoutId = 1;
        $blockCreateStruct->status = VAlue::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 0;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->placeholderParameters = array(
            'left' => array(
                'param' => 'Value',
            ),
        );

        $blockCreateStruct->parameters = array(
            'a_param' => 'A value',
        );

        $this->assertEquals(
            new Block(
                array(
                    'id' => 38,
                    'layoutId' => 1,
                    'depth' => 0,
                    'path' => '/38/',
                    'parentId' => null,
                    'placeholder' => null,
                    'position' => null,
                    'definitionIdentifier' => 'new_block',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'placeholderParameters' => array(
                        'left' => array(
                            'param' => 'Value',
                        ),
                    ),
                )
            ),
            $this->blockHandler->createBlock($blockCreateStruct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCreateBlockWithNoPosition()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->layoutId = 1;
        $blockCreateStruct->status = VAlue::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->placeholderParameters = array(
            'left' => array(
                'param' => 'Value',
            ),
        );

        $blockCreateStruct->parameters = array(
            'a_param' => 'A value',
        );

        $this->assertEquals(
            new Block(
                array(
                    'id' => 38,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/38/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 2,
                    'definitionIdentifier' => 'new_block',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'placeholderParameters' => array(
                        'left' => array(
                            'param' => 'Value',
                        ),
                    ),
                )
            ),
            $this->blockHandler->createBlock(
                $blockCreateStruct,
                $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
                'root'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateBlockThrowsBadStateExceptionOnNegativePosition()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->layoutId = 1;
        $blockCreateStruct->status = VAlue::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = -5;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->placeholderParameters = array(
            'left' => array(
                'parameters' => array(
                    'param' => 'Value',
                ),
            ),
        );

        $blockCreateStruct->parameters = array(
            'a_param' => 'A value',
        );

        $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->blockHandler->loadBlock(3, Value::STATUS_DRAFT),
            'root'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateBlockThrowsBadStateExceptionOnTooLargePosition()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->layoutId = 1;
        $blockCreateStruct->status = VAlue::STATUS_DRAFT;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 9999;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->placeholderParameters = array(
            'left' => array(
                'parameters' => array(
                    'param' => 'Value',
                ),
            ),
        );

        $blockCreateStruct->parameters = array(
            'a_param' => 'A value',
        );

        $this->blockHandler->createBlock(
            $blockCreateStruct,
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

        $this->blockHandler->createCollectionReference(
            $block,
            new CollectionReferenceCreateStruct(
                array(
                    'identifier' => 'new',
                    'collection' => $collection,
                    'offset' => 5,
                    'limit' => 10,
                )
            )
        );

        $this->assertEquals(
            new CollectionReference(
                array(
                    'blockId' => $block->id,
                    'blockStatus' => $block->status,
                    'collectionId' => $collection->id,
                    'collectionStatus' => $collection->status,
                    'identifier' => 'new',
                    'offset' => 5,
                    'limit' => 10,
                )
            ),
            $this->blockHandler->loadCollectionReference($block, 'new')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlock
     */
    public function testUpdateBlock()
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->name = 'My block';
        $blockUpdateStruct->viewType = 'large';
        $blockUpdateStruct->itemViewType = 'new';

        $blockUpdateStruct->placeholderParameters = array(
            'right' => array(
                'param' => 'Value',
            ),
        );

        $blockUpdateStruct->parameters = array(
            'number_of_columns' => 4,
            'some_param' => 'Some value',
        );

        $this->assertEquals(
            new Block(
                array(
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
                    'name' => 'My block',
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => array(
                        'number_of_columns' => 4,
                        'some_param' => 'Some value',
                    ),
                    'placeholderParameters' => array(
                        'right' => array(
                            'param' => 'Value',
                        ),
                    ),
                )
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
                array(
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
                    'status' => Value::STATUS_DRAFT,
                    'placeholderParameters' => array(),
                    'parameters' => array(
                        'number_of_columns' => 2,
                    ),
                )
            ),
            $this->blockHandler->updateBlock(
                $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
                $blockUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateCollectionReference
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateCollectionReference
     */
    public function testUpdateCollectionReference()
    {
        $this->assertEquals(
            new CollectionReference(
                array(
                    'blockId' => 31,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 2,
                    'collectionStatus' => Value::STATUS_PUBLISHED,
                    'identifier' => 'default',
                    'offset' => 3,
                    'limit' => 6,
                )
            ),
            $this->blockHandler->updateCollectionReference(
                $this->blockHandler->loadCollectionReference(
                    $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
                    'default'
                ),
                new CollectionReferenceUpdateStruct(
                    array(
                        'collection' => new Collection(array('id' => 2, 'status' => Value::STATUS_PUBLISHED)),
                        'offset' => 3,
                        'limit' => 6,
                    )
                )
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateCollectionReference
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateCollectionReference
     */
    public function testUpdateCollectionReferenceWithDefaultValues()
    {
        $this->assertEquals(
            new CollectionReference(
                array(
                    'blockId' => 31,
                    'blockStatus' => Value::STATUS_DRAFT,
                    'collectionId' => 1,
                    'collectionStatus' => Value::STATUS_DRAFT,
                    'identifier' => 'default',
                    'offset' => 0,
                    'limit' => null,
                )
            ),
            $this->blockHandler->updateCollectionReference(
                $this->blockHandler->loadCollectionReference(
                    $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
                    'default'
                ),
                new CollectionReferenceUpdateStruct()
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
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
                array(
                    'id' => 38,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/38/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 2,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Value::STATUS_DRAFT,
                    'placeholderParameters' => array(),
                    'parameters' => array(
                        'number_of_columns' => 2,
                    ),
                )
            ),
            $copiedBlock
        );

        $this->assertEquals(
            array(
                new CollectionReference(
                    array(
                        'blockId' => 38,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 7,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'default',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
                new CollectionReference(
                    array(
                        'blockId' => 38,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 3,
                        'collectionStatus' => Value::STATUS_PUBLISHED,
                        'identifier' => 'featured',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
            ),
            $this->blockHandler->loadCollectionReferences($copiedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
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
                array(
                    'id' => 38,
                    'layoutId' => 2,
                    'depth' => 1,
                    'path' => '/7/38/',
                    'parentId' => 7,
                    'placeholder' => 'root',
                    'position' => 2,
                    'definitionIdentifier' => 'text',
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My third block',
                    'status' => Value::STATUS_DRAFT,
                    'placeholderParameters' => array(
                        'main' => array(),
                    ),
                    'parameters' => array(
                        'content' => 'Text',
                    ),
                )
            ),
            $copiedBlock
        );

        $copiedSubBlock = $this->blockHandler->loadBlock(39, Value::STATUS_DRAFT);

        $this->assertEquals(
            new Block(
                array(
                    'id' => 39,
                    'layoutId' => 2,
                    'depth' => 2,
                    'path' => '/7/38/39/',
                    'parentId' => 38,
                    'placeholder' => 'main',
                    'position' => 0,
                    'definitionIdentifier' => 'text',
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My seventh block',
                    'status' => Value::STATUS_DRAFT,
                    'placeholderParameters' => array(),
                    'parameters' => array(
                        'content' => 'Text',
                    ),
                )
            ),
            $copiedSubBlock
        );

        $this->assertEquals(
            array(
                new CollectionReference(
                    array(
                        'blockId' => 39,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 7,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'default',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
            ),
            $this->blockHandler->loadCollectionReferences($copiedSubBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
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
                array(
                    'id' => 38,
                    'layoutId' => 2,
                    'depth' => 1,
                    'path' => '/8/38/',
                    'parentId' => 8,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Value::STATUS_DRAFT,
                    'placeholderParameters' => array(),
                    'parameters' => array(
                        'number_of_columns' => 2,
                    ),
                )
            ),
            $copiedBlock
        );

        $this->assertEquals(
            array(
                new CollectionReference(
                    array(
                        'blockId' => 38,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 7,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'default',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
                new CollectionReference(
                    array(
                        'blockId' => 38,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 3,
                        'collectionStatus' => Value::STATUS_PUBLISHED,
                        'identifier' => 'featured',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
            ),
            $this->blockHandler->loadCollectionReferences($copiedBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCopyBlockCollections()
    {
        $targetBlock = $this->blockHandler->loadBlock(33, Value::STATUS_DRAFT);

        $this->blockHandler->copyBlockCollections(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            $targetBlock
        );

        $this->assertEquals(
            array(
                new CollectionReference(
                    array(
                        'blockId' => 33,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 7,
                        'collectionStatus' => Value::STATUS_DRAFT,
                        'identifier' => 'default',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
                new CollectionReference(
                    array(
                        'blockId' => 33,
                        'blockStatus' => Value::STATUS_DRAFT,
                        'collectionId' => 3,
                        'collectionStatus' => Value::STATUS_PUBLISHED,
                        'identifier' => 'featured',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
            ),
            $this->blockHandler->loadCollectionReferences($targetBlock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testMoveBlock()
    {
        $this->assertEquals(
            new Block(
                array(
                    'id' => 33,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/4/33/',
                    'parentId' => 4,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'text',
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My third block',
                    'status' => Value::STATUS_DRAFT,
                    'placeholderParameters' => array(
                        'main' => array(),
                    ),
                    'parameters' => array(
                        'content' => 'Text',
                    ),
                )
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
                array(
                    'id' => 37,
                    'layoutId' => 1,
                    'depth' => 2,
                    'path' => '/4/33/37/',
                    'parentId' => 33,
                    'placeholder' => 'main',
                    'position' => 0,
                    'definitionIdentifier' => 'text',
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My seventh block',
                    'status' => Value::STATUS_DRAFT,
                    'placeholderParameters' => array(),
                    'parameters' => array(
                        'content' => 'Text',
                    ),
                )
            ),
            $this->blockHandler->loadBlock(37, Value::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testMoveBlockToPosition()
    {
        $this->assertEquals(
            new Block(
                array(
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
                    'status' => Value::STATUS_DRAFT,
                    'placeholderParameters' => array(),
                    'parameters' => array(
                        'number_of_columns' => 2,
                    ),
                )
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testMoveBlockToLowerPosition()
    {
        $this->assertEquals(
            new Block(
                array(
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
                    'status' => Value::STATUS_DRAFT,
                    'placeholderParameters' => array(),
                    'parameters' => array(
                        'number_of_columns' => 3,
                    ),
                )
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
     */
    public function testMoveBlockToPositionThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlockStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockCollectionsStatus
     */
    public function testCreateBlockStatus()
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT)
        );

        $this->blockHandler->createBlockStatus(
            $this->blockHandler->loadBlock(31, Value::STATUS_PUBLISHED),
            Value::STATUS_DRAFT
        );

        $this->assertEquals(
            new Block(
                array(
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
                    'status' => Value::STATUS_DRAFT,
                    'placeholderParameters' => array(),
                    'parameters' => array(
                        'number_of_columns' => 3,
                    ),
                )
            ),
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT)
        );

        $collectionReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT)
        );

        $this->assertCount(2, $collectionReferences);

        $collectionIds = array(
            $collectionReferences[0]->collectionId,
            $collectionReferences[1]->collectionId,
        );

        $this->assertContains(2, $collectionIds);
        $this->assertContains(3, $collectionIds);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadSubBlockIds
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockCollectionIds
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteCollectionReferences
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteBlocks()
    {
        $this->blockHandler->deleteBlocks(
            array(31, 32),
            Value::STATUS_DRAFT
        );

        try {
            $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
            self::fail('Block still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(32, Value::STATUS_DRAFT);
            self::fail('Block still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that shared collection still exists
        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);

        // This should throw NotFoundException
        $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlockCollections
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockCollectionIds
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteCollectionReferences
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteBlockCollections()
    {
        $this->blockHandler->deleteBlockCollections(
            array(31, 32),
            Value::STATUS_DRAFT
        );

        // Verify that shared collection still exists
        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);

        // This should throw NotFoundException
        $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
    }
}
