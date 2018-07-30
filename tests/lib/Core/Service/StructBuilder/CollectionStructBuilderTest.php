<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder;
use Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;

abstract class CollectionStructBuilderTest extends ServiceTestCase
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder
     */
    private $structBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->collectionService = $this->createCollectionService();

        $this->structBuilder = new CollectionStructBuilder(new ConfigStructBuilder());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::__construct
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newCollectionCreateStruct
     */
    public function testNewCollectionCreateStruct(): void
    {
        $queryCreateStruct = new QueryCreateStruct(new QueryType('my_query_type'));
        $struct = $this->structBuilder->newCollectionCreateStruct($queryCreateStruct);

        $this->assertInstanceOf(CollectionCreateStruct::class, $struct);

        $this->assertSame(
            [
                'offset' => 0,
                'limit' => null,
                'queryCreateStruct' => $queryCreateStruct,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStruct(): void
    {
        $struct = $this->structBuilder->newCollectionUpdateStruct();

        $this->assertInstanceOf(CollectionUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'offset' => null,
                'limit' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithCollection(): void
    {
        $struct = $this->structBuilder->newCollectionUpdateStruct(
            $this->collectionService->loadCollectionDraft(3)
        );

        $this->assertInstanceOf(CollectionUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'offset' => 4,
                'limit' => 2,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithUnlimitedCollection(): void
    {
        $struct = $this->structBuilder->newCollectionUpdateStruct(
            $this->collectionService->loadCollectionDraft(1)
        );

        $this->assertInstanceOf(CollectionUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'offset' => 0,
                'limit' => 0,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newItemCreateStruct
     */
    public function testNewItemCreateStruct(): void
    {
        $itemDefinition = new ItemDefinition();
        $struct = $this->structBuilder->newItemCreateStruct($itemDefinition, '42');

        $this->assertInstanceOf(ItemCreateStruct::class, $struct);

        $this->assertSame(
            [
                'definition' => $itemDefinition,
                'value' => '42',
                'configStructs' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newItemUpdateStruct
     */
    public function testNewItemUpdateStruct(): void
    {
        $struct = $this->structBuilder->newItemUpdateStruct();

        $this->assertInstanceOf(ItemUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'configStructs' => [],
            ],
            $this->exportObject($struct, true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newItemUpdateStruct
     */
    public function testNewItemUpdateStructFromItem(): void
    {
        $item = $this->collectionService->loadItemDraft(1);
        $struct = $this->structBuilder->newItemUpdateStruct($item);

        $this->assertInstanceOf(ItemUpdateStruct::class, $struct);

        $this->assertArrayHasKey('key', $struct->getConfigStructs());
        $this->assertInstanceOf(ConfigStruct::class, $struct->getConfigStruct('key'));

        $this->assertSame(
            [
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

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newQueryCreateStruct
     */
    public function testNewQueryCreateStruct(): void
    {
        $queryType = new QueryType('my_query_type');

        $struct = $this->structBuilder->newQueryCreateStruct($queryType);

        $this->assertInstanceOf(QueryCreateStruct::class, $struct);

        $this->assertSame(
            [
                'queryType' => $queryType,
                'parameterValues' => [
                    'param' => 'value',
                    'param2' => null,
                ],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStruct(): void
    {
        $struct = $this->structBuilder->newQueryUpdateStruct('en');

        $this->assertInstanceOf(QueryUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'locale' => 'en',
                'parameterValues' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStructFromQuery(): void
    {
        $query = $this->collectionService->loadQueryDraft(4);
        $struct = $this->structBuilder->newQueryUpdateStruct('en', $query);

        $this->assertInstanceOf(QueryUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'locale' => 'en',
                'parameterValues' => [
                    'param' => null,
                    'param2' => null,
                ],
            ],
            $this->exportObject($struct)
        );
    }
}
