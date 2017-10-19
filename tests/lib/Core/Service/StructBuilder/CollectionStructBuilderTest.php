<?php

namespace Netgen\BlockManager\Tests\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class CollectionStructBuilderTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder
     */
    private $structBuilder;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->collectionService = $this->createCollectionService();

        $this->structBuilder = new CollectionStructBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStruct()
    {
        $this->assertEquals(
            new CollectionUpdateStruct(
                array(
                    'offset' => null,
                    'limit' => null,
                )
            ),
            $this->collectionService->newCollectionUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithCollection()
    {
        $this->assertEquals(
            new CollectionUpdateStruct(
                array(
                    'offset' => 4,
                    'limit' => 2,
                )
            ),
            $this->collectionService->newCollectionUpdateStruct(
                $this->collectionService->loadCollectionDraft(3)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithUnlimitedCollection()
    {
        $this->assertEquals(
            new CollectionUpdateStruct(
                array(
                    'offset' => 0,
                    'limit' => 0,
                )
            ),
            $this->collectionService->newCollectionUpdateStruct(
                $this->collectionService->loadCollectionDraft(1)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newItemCreateStruct
     */
    public function testNewItemCreateStruct()
    {
        $this->assertEquals(
            new ItemCreateStruct(
                array(
                    'type' => Item::TYPE_OVERRIDE,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                )
            ),
            $this->structBuilder->newItemCreateStruct(Item::TYPE_OVERRIDE, '42', 'ezcontent')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newQueryCreateStruct
     */
    public function testNewQueryCreateStruct()
    {
        $queryCreateStruct = $this->structBuilder->newQueryCreateStruct(
            new QueryType('ezcontent_search')
        );

        $this->assertEquals(
            new QueryCreateStruct(
                array(
                    'queryType' => new QueryType('ezcontent_search'),
                    'parameterValues' => array(
                        'param' => null,
                        'param2' => null,
                    ),
                )
            ),
            $queryCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStruct()
    {
        $this->assertEquals(
            new QueryUpdateStruct(
                array(
                    'locale' => 'en',
                )
            ),
            $this->structBuilder->newQueryUpdateStruct('en')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStructFromQuery()
    {
        $query = $this->collectionService->loadQueryDraft(4);

        $this->assertEquals(
            new QueryUpdateStruct(
                array(
                    'locale' => 'en',
                    'parameterValues' => array(
                        'param' => null,
                        'param2' => 0,
                    ),
                )
            ),
            $this->structBuilder->newQueryUpdateStruct('en', $query)
        );
    }
}
