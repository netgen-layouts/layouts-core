<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder;

use Netgen\Layouts\API\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder;
use Netgen\Layouts\Core\StructBuilder\ConfigStructBuilder;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Ramsey\Uuid\Uuid;

abstract class CollectionStructBuilderTest extends CoreTestCase
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder
     */
    private $structBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new CollectionStructBuilder(new ConfigStructBuilder());
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::__construct
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::newCollectionCreateStruct
     */
    public function testNewCollectionCreateStruct(): void
    {
        $queryCreateStruct = new QueryCreateStruct(new QueryType('my_query_type'));
        $struct = $this->structBuilder->newCollectionCreateStruct($queryCreateStruct);

        self::assertSame(
            [
                'offset' => 0,
                'limit' => null,
                'queryCreateStruct' => $queryCreateStruct,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStruct(): void
    {
        $struct = $this->structBuilder->newCollectionUpdateStruct();

        self::assertSame(
            [
                'offset' => null,
                'limit' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithCollection(): void
    {
        $struct = $this->structBuilder->newCollectionUpdateStruct(
            $this->collectionService->loadCollectionDraft(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89'))
        );

        self::assertSame(
            [
                'offset' => 4,
                'limit' => 2,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStructWithUnlimitedCollection(): void
    {
        $struct = $this->structBuilder->newCollectionUpdateStruct(
            $this->collectionService->loadCollectionDraft(Uuid::fromString('a79dde13-1f5c-51a6-bea9-b766236be49e'))
        );

        self::assertSame(
            [
                'offset' => 0,
                'limit' => 0,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::newItemCreateStruct
     */
    public function testNewItemCreateStruct(): void
    {
        $itemDefinition = new ItemDefinition();
        $struct = $this->structBuilder->newItemCreateStruct($itemDefinition, '42');

        self::assertSame(
            [
                'definition' => $itemDefinition,
                'value' => '42',
                'configStructs' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::newItemUpdateStruct
     */
    public function testNewItemUpdateStruct(): void
    {
        $struct = $this->structBuilder->newItemUpdateStruct();

        self::assertSame(
            [
                'configStructs' => [],
            ],
            $this->exportObject($struct, true)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::newItemUpdateStruct
     */
    public function testNewItemUpdateStructFromItem(): void
    {
        $item = $this->collectionService->loadItemDraft(1);
        $struct = $this->structBuilder->newItemUpdateStruct($item);

        self::assertArrayHasKey('key', $struct->getConfigStructs());

        self::assertSame(
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
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::newQueryCreateStruct
     */
    public function testNewQueryCreateStruct(): void
    {
        $queryType = new QueryType('my_query_type');

        $struct = $this->structBuilder->newQueryCreateStruct($queryType);

        self::assertSame(
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
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStruct(): void
    {
        $struct = $this->structBuilder->newQueryUpdateStruct('en');

        self::assertSame(
            [
                'locale' => 'en',
                'parameterValues' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStructFromQuery(): void
    {
        $query = $this->collectionService->loadQueryDraft(4);
        $struct = $this->structBuilder->newQueryUpdateStruct('en', $query);

        self::assertSame(
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
