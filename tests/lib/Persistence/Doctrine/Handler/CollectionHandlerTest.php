<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryTranslationUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\TestCase;

final class CollectionHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler
     */
    private $collectionHandler;

    public function setUp()
    {
        $this->createDatabase();

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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getCollectionSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     */
    public function testLoadCollection()
    {
        $this->assertEquals(
            new Collection(
                array(
                    'id' => 1,
                    'status' => Value::STATUS_DRAFT,
                    'offset' => 0,
                    'limit' => null,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                )
            ),
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "999999"
     */
    public function testLoadCollectionThrowsNotFoundException()
    {
        $this->collectionHandler->loadCollection(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getItemSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     */
    public function testLoadItem()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '72',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array(),
                )
            ),
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find item with identifier "999999"
     */
    public function testLoadItemThrowsNotFoundException()
    {
        $this->collectionHandler->loadItem(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItemWithPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getItemSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemWithPositionData
     */
    public function testLoadItemWithPosition()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '72',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array(),
                )
            ),
            $this->collectionHandler->loadItemWithPosition(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                0
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadItemWithPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadItemWithPositionData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find item in collection with ID "1" at position 9999
     */
    public function testLoadItemWithPositionThrowsNotFoundException()
    {
        $this->collectionHandler->loadItemWithPosition(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testLoadCollectionItems()
    {
        $items = $this->collectionHandler->loadCollectionItems(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );

        $this->assertNotEmpty($items);

        foreach ($items as $item) {
            $this->assertInstanceOf(Item::class, $item);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::getQuerySelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     */
    public function testLoadQuery()
    {
        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
                    'type' => 'ezcontent_search',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                    'parameters' => array(
                        'en' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                        'hr' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadQueryData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "999999"
     */
    public function testLoadQueryThrowsNotFoundException()
    {
        $this->collectionHandler->loadQuery(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testLoadCollectionQuery()
    {
        $query = $this->collectionHandler->loadCollectionQuery(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED)
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
                    'type' => 'ezcontent_search',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                    'parameters' => array(
                        'en' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                        'hr' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $query
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::loadCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query for collection with identifier "1"
     */
    public function testLoadCollectionQueryThrowsNotFoundException()
    {
        $this->collectionHandler->loadCollectionQuery(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionExists()
    {
        $this->assertTrue($this->collectionHandler->collectionExists(1, Value::STATUS_DRAFT));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionNotExists()
    {
        $this->assertFalse($this->collectionHandler->collectionExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::collectionExists
     */
    public function testCollectionNotExistsInStatus()
    {
        $this->assertFalse($this->collectionHandler->collectionExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     */
    public function testCreateCollection()
    {
        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->status = Value::STATUS_DRAFT;
        $collectionCreateStruct->offset = 5;
        $collectionCreateStruct->limit = 10;
        $collectionCreateStruct->mainLocale = 'en';
        $collectionCreateStruct->isTranslatable = true;
        $collectionCreateStruct->alwaysAvailable = true;

        $createdCollection = $this->collectionHandler->createCollection($collectionCreateStruct);

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 7,
                    'status' => Value::STATUS_DRAFT,
                    'offset' => 5,
                    'limit' => 10,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en'),
                    'alwaysAvailable' => true,
                )
            ),
            $createdCollection
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQueryTranslation
     */
    public function testCreateCollectionTranslation()
    {
        $collection = $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de',
            'en'
        );

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 2,
                    'status' => Value::STATUS_PUBLISHED,
                    'offset' => 0,
                    'limit' => null,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr', 'de'),
                    'alwaysAvailable' => true,
                )
            ),
            $collection
        );

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => $collection->id,
                    'type' => 'ezcontent_search',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('de', 'en', 'hr'),
                    'alwaysAvailable' => true,
                    'parameters' => array(
                        'en' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                        'hr' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                        'de' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $query
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     */
    public function testCreateCollectionTranslationWithNonMainSourceLocale()
    {
        $collection = $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de',
            'hr'
        );

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 2,
                    'status' => Value::STATUS_PUBLISHED,
                    'offset' => 0,
                    'limit' => null,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr', 'de'),
                    'alwaysAvailable' => true,
                )
            ),
            $collection
        );

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => $collection->id,
                    'type' => 'ezcontent_search',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('de', 'en', 'hr'),
                    'alwaysAvailable' => true,
                    'parameters' => array(
                        'en' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                        'hr' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                        'de' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $query
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     */
    public function testCreateCollectionTranslationForCollectionWithNoQuery()
    {
        $collection = $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            'de',
            'en'
        );

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 1,
                    'status' => Value::STATUS_DRAFT,
                    'offset' => 0,
                    'limit' => null,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr', 'de'),
                    'alwaysAvailable' => true,
                )
            ),
            $collection
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Collection already has the provided locale.
     */
    public function testCreateCollectionTranslationThrowsBadStateExceptionWithExistingLocale()
    {
        $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'en',
            'hr'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollectionTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Collection does not have the provided source locale.
     */
    public function testCreateCollectionTranslationThrowsBadStateExceptionWithNonExistingSourceLocale()
    {
        $this->collectionHandler->createCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de',
            'fr'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::setMainTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testSetMainTranslation()
    {
        $collection = $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);
        $collection = $this->collectionHandler->setMainTranslation($collection, 'hr');

        $this->assertEquals('hr', $collection->mainLocale);

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);
        $this->assertEquals('hr', $query->mainLocale);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::setMainTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testSetMainTranslationForCollectionWithNoQuery()
    {
        $collection = $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
        $collection = $this->collectionHandler->setMainTranslation($collection, 'hr');

        $this->assertEquals('hr', $collection->mainLocale);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::setMainTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "mainLocale" has an invalid state. Collection does not have the provided locale.
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale()
    {
        $collection = $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);
        $this->collectionHandler->setMainTranslation($collection, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollection()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();
        $collectionUpdateStruct->offset = 5;
        $collectionUpdateStruct->limit = 10;
        $collectionUpdateStruct->isTranslatable = false;
        $collectionUpdateStruct->alwaysAvailable = false;

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 1,
                    'status' => Value::STATUS_DRAFT,
                    'offset' => 5,
                    'limit' => 10,
                    'isTranslatable' => false,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => false,
                )
            ),
            $this->collectionHandler->updateCollection(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                $collectionUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollectionWithNoLimit()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();
        $collectionUpdateStruct->offset = 5;
        $collectionUpdateStruct->limit = 0;

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 3,
                    'status' => Value::STATUS_DRAFT,
                    'offset' => 5,
                    'limit' => null,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                )
            ),
            $this->collectionHandler->updateCollection(
                $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
                $collectionUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateCollection
     */
    public function testUpdateCollectionWithDefaultValues()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 1,
                    'status' => Value::STATUS_DRAFT,
                    'offset' => 0,
                    'limit' => null,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                )
            ),
            $this->collectionHandler->updateCollection(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                $collectionUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testCopyCollection()
    {
        $copiedCollection = $this->collectionHandler->copyCollection(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED)
        );

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 7,
                    'status' => Value::STATUS_PUBLISHED,
                    'offset' => 4,
                    'limit' => 2,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                )
            ),
            $copiedCollection
        );

        $this->assertEquals(
            array(
                new Item(
                    array(
                        'id' => 13,
                        'collectionId' => $copiedCollection->id,
                        'position' => 2,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '72',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_PUBLISHED,
                        'config' => array(),
                    )
                ),
                new Item(
                    array(
                        'id' => 14,
                        'collectionId' => $copiedCollection->id,
                        'position' => 3,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '73',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_PUBLISHED,
                        'config' => array(),
                    )
                ),
                new Item(
                    array(
                        'id' => 15,
                        'collectionId' => $copiedCollection->id,
                        'position' => 5,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '74',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_PUBLISHED,
                        'config' => array(),
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems($copiedCollection)
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 5,
                    'collectionId' => $copiedCollection->id,
                    'type' => 'ezcontent_search',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                    'parameters' => array(
                        'en' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                        'hr' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->loadCollectionQuery($copiedCollection)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::copyCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testCopyCollectionWithoutQuery()
    {
        $copiedCollection = $this->collectionHandler->copyCollection(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT)
        );

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 7,
                    'status' => Value::STATUS_DRAFT,
                    'offset' => 0,
                    'limit' => null,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                )
            ),
            $copiedCollection
        );

        $this->assertEquals(
            array(
                new Item(
                    array(
                        'id' => 13,
                        'collectionId' => $copiedCollection->id,
                        'position' => 0,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '72',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_DRAFT,
                        'config' => array(),
                    )
                ),
                new Item(
                    array(
                        'id' => 14,
                        'collectionId' => $copiedCollection->id,
                        'position' => 1,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '73',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_DRAFT,
                        'config' => array(),
                    )
                ),
                new Item(
                    array(
                        'id' => 15,
                        'collectionId' => $copiedCollection->id,
                        'position' => 2,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '74',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_DRAFT,
                        'config' => array(),
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems($copiedCollection)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryData
     */
    public function testCreateCollectionStatus()
    {
        $copiedCollection = $this->collectionHandler->createCollectionStatus(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            Value::STATUS_ARCHIVED
        );

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 3,
                    'status' => Value::STATUS_ARCHIVED,
                    'offset' => 4,
                    'limit' => 2,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                )
            ),
            $copiedCollection
        );

        $this->assertEquals(
            array(
                new Item(
                    array(
                        'id' => 7,
                        'collectionId' => 3,
                        'position' => 2,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '72',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_ARCHIVED,
                        'config' => array(),
                    )
                ),
                new Item(
                    array(
                        'id' => 8,
                        'collectionId' => 3,
                        'position' => 3,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '73',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_ARCHIVED,
                        'config' => array(),
                    )
                ),
                new Item(
                    array(
                        'id' => 9,
                        'collectionId' => 3,
                        'position' => 5,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '74',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_ARCHIVED,
                        'config' => array(),
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems($copiedCollection)
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 2,
                    'collectionId' => 3,
                    'type' => 'ezcontent_search',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                    'parameters' => array(
                        'en' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                        'hr' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                    ),
                    'status' => Value::STATUS_ARCHIVED,
                )
            ),
            $this->collectionHandler->loadCollectionQuery($copiedCollection)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createCollectionStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionItemsData
     */
    public function testCreateCollectionStatusWithoutQuery()
    {
        $copiedCollection = $this->collectionHandler->createCollectionStatus(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            Value::STATUS_ARCHIVED
        );

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 1,
                    'status' => Value::STATUS_ARCHIVED,
                    'offset' => 0,
                    'limit' => null,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                )
            ),
            $copiedCollection
        );

        $this->assertEquals(
            array(
                new Item(
                    array(
                        'id' => 1,
                        'collectionId' => 1,
                        'position' => 0,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '72',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_ARCHIVED,
                        'config' => array(),
                    )
                ),
                new Item(
                    array(
                        'id' => 2,
                        'collectionId' => 1,
                        'position' => 1,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '73',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_ARCHIVED,
                        'config' => array(),
                    )
                ),
                new Item(
                    array(
                        'id' => 3,
                        'collectionId' => 1,
                        'position' => 2,
                        'type' => Item::TYPE_MANUAL,
                        'value' => '74',
                        'valueType' => 'ezlocation',
                        'status' => Value::STATUS_ARCHIVED,
                        'config' => array(),
                    )
                ),
            ),
            $this->collectionHandler->loadCollectionItems($copiedCollection)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "3"
     */
    public function testDeleteCollection()
    {
        $this->collectionHandler->deleteCollection(3);

        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollection
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection with identifier "3"
     */
    public function testDeleteCollectionInOneStatus()
    {
        $this->collectionHandler->deleteCollection(3, Value::STATUS_DRAFT);

        // First, verify that NOT all collection statuses are deleted
        try {
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the collection in draft status deleted other/all statuses.');
        }

        $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQueryTranslations
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     */
    public function testDeleteCollectionTranslation()
    {
        $collection = $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'hr'
        );

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 2,
                    'status' => Value::STATUS_PUBLISHED,
                    'offset' => 0,
                    'limit' => null,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en'),
                    'alwaysAvailable' => true,
                )
            ),
            $collection
        );

        $query = $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED);

        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => $collection->id,
                    'type' => 'ezcontent_search',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en'),
                    'alwaysAvailable' => true,
                    'parameters' => array(
                        'en' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $query
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     */
    public function testDeleteCollectionTranslationForCollectionWithNoQuery()
    {
        $collection = $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            'hr'
        );

        $this->assertEquals(
            new Collection(
                array(
                    'id' => 1,
                    'status' => Value::STATUS_DRAFT,
                    'offset' => 0,
                    'limit' => null,
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en'),
                    'alwaysAvailable' => true,
                )
            ),
            $collection
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Collection does not have the provided locale.
     */
    public function testDeleteCollectionTranslationWithNonExistingLocale()
    {
        $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'de'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteCollectionTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Main translation cannot be removed from the collection.
     */
    public function testDeleteCollectionTranslationWithMainLocale()
    {
        $this->collectionHandler->deleteCollectionTranslation(
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED),
            'en'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItem()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = 1;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'ezcontent';
        $itemCreateStruct->config = array('config' => array('value' => 42));

        $this->assertEquals(
            new Item(
                array(
                    'id' => 13,
                    'collectionId' => 1,
                    'position' => 1,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '42',
                    'valueType' => 'ezcontent',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array('config' => array('value' => 42)),
                )
            ),
            $this->collectionHandler->addItem(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                $itemCreateStruct
            )
        );

        $secondItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $this->assertEquals(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemToDynamicCollection()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = 2;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'ezcontent';
        $itemCreateStruct->config = array('config' => array('value' => 42));

        $this->assertEquals(
            new Item(
                array(
                    'id' => 13,
                    'collectionId' => 3,
                    'position' => 2,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '42',
                    'valueType' => 'ezcontent',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array('config' => array('value' => 42)),
                )
            ),
            $this->collectionHandler->addItem(
                $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
                $itemCreateStruct
            )
        );

        $secondItem = $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);
        $this->assertEquals(3, $secondItem->position);

        $thirdItem = $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT);
        $this->assertEquals(4, $thirdItem->position);

        $fourthItem = $this->collectionHandler->loadItem(9, Value::STATUS_DRAFT);
        $this->assertEquals(5, $fourthItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemToDynamicCollectionInEmptySlot()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = 4;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'ezcontent';
        $itemCreateStruct->config = array('config' => array('value' => 42));

        $this->assertEquals(
            new Item(
                array(
                    'id' => 13,
                    'collectionId' => 3,
                    'position' => 4,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '42',
                    'valueType' => 'ezcontent',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array('config' => array('value' => 42)),
                )
            ),
            $this->collectionHandler->addItem(
                $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT),
                $itemCreateStruct
            )
        );

        $secondItem = $this->collectionHandler->loadItem(9, Value::STATUS_DRAFT);
        $this->assertEquals(5, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     */
    public function testAddItemWithNoPosition()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'ezcontent';
        $itemCreateStruct->config = array('config' => array('value' => 42));

        $this->assertEquals(
            new Item(
                array(
                    'id' => 13,
                    'collectionId' => 1,
                    'position' => 3,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '42',
                    'valueType' => 'ezcontent',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array('config' => array('value' => 42)),
                )
            ),
            $this->collectionHandler->addItem(
                $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
                $itemCreateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
     */
    public function testAddItemThrowsBadStateExceptionOnNegativePosition()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = -1;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'ezcontent';
        $itemCreateStruct->config = array();

        $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $itemCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::addItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createItemPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testAddItemThrowsBadStateExceptionOnTooLargePosition()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_MANUAL;
        $itemCreateStruct->position = 9999;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'ezcontent';
        $itemCreateStruct->config = array();

        $this->collectionHandler->addItem(
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT),
            $itemCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testUpdateItem()
    {
        $itemUpdateStruct = new ItemUpdateStruct();
        $itemUpdateStruct->config = array(
            'new_config' => array(
                'val' => 24,
            ),
        );

        $this->assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '72',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array('new_config' => array('val' => 24)),
                )
            ),
            $this->collectionHandler->updateItem(
                $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
                $itemUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItem()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 12,
                    'collectionId' => 4,
                    'position' => 2,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '74',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array(),
                )
            ),
            $this->collectionHandler->moveItem(
                $this->collectionHandler->loadItem(12, Value::STATUS_DRAFT),
                2
            )
        );

        $firstItem = $this->collectionHandler->loadItem(10, Value::STATUS_DRAFT);
        $this->assertEquals(3, $firstItem->position);

        $secondItem = $this->collectionHandler->loadItem(11, Value::STATUS_DRAFT);
        $this->assertEquals(4, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemWithSwitchingPositions()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 1,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '72',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array(),
                )
            ),
            $this->collectionHandler->moveItem(
                $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
                1
            )
        );

        $firstItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $this->assertEquals(0, $firstItem->position);

        $secondItem = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);
        $this->assertEquals(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToSamePosition()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 1,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '72',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array(),
                )
            ),
            $this->collectionHandler->moveItem(
                $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
                0
            )
        );

        $firstItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $this->assertEquals(1, $firstItem->position);

        $firstItem = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);
        $this->assertEquals(2, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToLowerPosition()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 2,
                    'collectionId' => 1,
                    'position' => 0,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '73',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array(),
                )
            ),
            $this->collectionHandler->moveItem(
                $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT),
                0
            )
        );

        $firstItem = $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT);
        $this->assertEquals(1, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToLowerPositionWithSwitchingPositions()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 3,
                    'collectionId' => 1,
                    'position' => 1,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '74',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array(),
                )
            ),
            $this->collectionHandler->moveItem(
                $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT),
                1
            )
        );

        $firstItem = $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT);
        $this->assertEquals(0, $firstItem->position);

        $secondItem = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $this->assertEquals(2, $secondItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemInDynamicCollection()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 7,
                    'collectionId' => 3,
                    'position' => 4,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '72',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array(),
                )
            ),
            $this->collectionHandler->moveItem(
                $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT),
                4
            )
        );

        $secondItem = $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT);
        $this->assertEquals(3, $secondItem->position);

        $thirdItem = $this->collectionHandler->loadItem(9, Value::STATUS_DRAFT);
        $this->assertEquals(5, $thirdItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testMoveItemToLowerPositionInDynamicCollection()
    {
        $this->assertEquals(
            new Item(
                array(
                    'id' => 8,
                    'collectionId' => 3,
                    'position' => 2,
                    'type' => Item::TYPE_MANUAL,
                    'value' => '73',
                    'valueType' => 'ezlocation',
                    'status' => Value::STATUS_DRAFT,
                    'config' => array(),
                )
            ),
            $this->collectionHandler->moveItem(
                $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT),
                2
            )
        );

        $firstItem = $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);
        $this->assertEquals(3, $firstItem->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position cannot be negative.
     */
    public function testMoveItemThrowsBadStateExceptionOnNegativePosition()
    {
        $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            -1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::incrementItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::moveItemToPosition
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testMoveItemThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->collectionHandler->moveItem(
            $this->collectionHandler->loadItem(1, Value::STATUS_DRAFT),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::switchItemPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateItem
     */
    public function testSwitchItemPositions()
    {
        $item1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $item2 = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);

        $this->collectionHandler->switchItemPositions($item1, $item2);

        $updatedItem1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $updatedItem2 = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);

        $this->assertEquals($item2->position, $updatedItem1->position);
        $this->assertEquals($item1->position, $updatedItem2->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::switchItemPositions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage First and second items are the same.
     */
    public function testSwitchItemPositionsThrowsBadStateExceptionWithSameItem()
    {
        $item1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $item2 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);

        $this->collectionHandler->switchItemPositions($item1, $item2);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::switchItemPositions
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Positions can be switched only for items within the same collection.
     */
    public function testSwitchItemPositionsThrowsBadStateExceptionWithItemsFromDifferentCollections()
    {
        $item1 = $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
        $item2 = $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);

        $this->collectionHandler->switchItemPositions($item1, $item2);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItem
     */
    public function testDeleteItem()
    {
        $this->collectionHandler->deleteItem(
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT)
        );

        $secondItem = $this->collectionHandler->loadItem(3, Value::STATUS_DRAFT);
        $this->assertEquals(1, $secondItem->position);

        try {
            $this->collectionHandler->loadItem(2, Value::STATUS_DRAFT);
            self::fail('Item still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteItem
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::getPositionHelperItemConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::isCollectionDynamic
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItem
     */
    public function testDeleteItemFromDynamicCollection()
    {
        $this->collectionHandler->deleteItem(
            $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT)
        );

        $secondItem = $this->collectionHandler->loadItem(8, Value::STATUS_DRAFT);
        $this->assertEquals(3, $secondItem->position);

        try {
            $this->collectionHandler->loadItem(7, Value::STATUS_DRAFT);
            self::fail('Item still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItems
     */
    public function testDeleteItems()
    {
        $collection = $this->collectionHandler->deleteItems(
            $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT)
        );

        $this->assertCount(0, $this->collectionHandler->loadCollectionItems($collection));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteItems
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteItems
     */
    public function testDeleteItemsWithSpecificItemType()
    {
        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_OVERRIDE;
        $itemCreateStruct->position = 2;
        $itemCreateStruct->value = '42';
        $itemCreateStruct->valueType = 'ezcontent';
        $itemCreateStruct->config = array();

        $collection = $this->collectionHandler->loadCollection(3, Value::STATUS_DRAFT);

        $this->collectionHandler->addItem($collection, $itemCreateStruct);

        $collection = $this->collectionHandler->deleteItems($collection, Item::TYPE_OVERRIDE);

        $collectionItems = $this->collectionHandler->loadCollectionItems($collection);
        $this->assertCount(3, $collectionItems);

        foreach ($collectionItems as $item) {
            $this->assertEquals(Item::TYPE_MANUAL, $item->type);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     */
    public function testCreateQuery()
    {
        $collection = $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);

        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->parameters = array(
            'param' => 'value',
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 5,
                    'collectionId' => $collection->id,
                    'type' => 'ezcontent_search',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                    'parameters' => array(
                        'en' => array(
                            'param' => 'value',
                        ),
                        'hr' => array(
                            'param' => 'value',
                        ),
                    ),
                    'status' => Value::STATUS_DRAFT,
                )
            ),
            $this->collectionHandler->createQuery(
                $collection,
                $queryCreateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::createQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::createQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Provided collection already has a query.
     */
    public function testCreateQueryThrowsBadStateExceptionWithExistingQuery()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->type = 'ezcontent_search';
        $queryCreateStruct->parameters = array(
            'param' => 'value',
        );

        $this->collectionHandler->createQuery(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED),
            $queryCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateQueryTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQueryTranslation
     */
    public function testUpdateQueryTranslation()
    {
        $translationUpdateStruct = new QueryTranslationUpdateStruct();

        $translationUpdateStruct->parameters = array(
            'parent_location_id' => 999,
            'some_param' => 'Some value',
        );

        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
                    'type' => 'ezcontent_search',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                    'parameters' => array(
                        'en' => array(
                            'parent_location_id' => 999,
                            'some_param' => 'Some value',
                        ),
                        'hr' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->updateQueryTranslation(
                $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED),
                'en',
                $translationUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateQueryTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQueryTranslation
     */
    public function testUpdateQueryTranslationWithDefaultValues()
    {
        $translationUpdateStruct = new QueryTranslationUpdateStruct();

        $this->assertEquals(
            new Query(
                array(
                    'id' => 1,
                    'collectionId' => 2,
                    'type' => 'ezcontent_search',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => array('en', 'hr'),
                    'alwaysAvailable' => true,
                    'parameters' => array(
                        'en' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                        'hr' => array(
                            'parent_location_id' => 2,
                            'sort_direction' => 'descending',
                            'sort_type' => 'date_published',
                            'query_type' => 'list',
                        ),
                    ),
                    'status' => Value::STATUS_PUBLISHED,
                )
            ),
            $this->collectionHandler->updateQueryTranslation(
                $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED),
                'en',
                $translationUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::updateQueryTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::updateQueryTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Query does not have the provided locale.
     */
    public function testUpdateQueryTranslationThrowsBadStateExceptionWithNonExistingLocale()
    {
        $this->collectionHandler->updateQueryTranslation(
            $this->collectionHandler->loadQuery(1, Value::STATUS_PUBLISHED),
            'de',
            new QueryTranslationUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler::deleteCollectionQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::deleteQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler::loadCollectionQueryIds
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find query with identifier "2"
     */
    public function testDeleteCollectionQuery()
    {
        $this->collectionHandler->deleteCollectionQuery(
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED)
        );

        // Query with ID 2 was in the collection with ID 3
        $this->collectionHandler->loadQuery(2, Value::STATUS_PUBLISHED);
    }
}
