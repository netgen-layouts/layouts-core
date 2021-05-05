<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemCreateStruct;
use Netgen\Layouts\API\Values\Collection\ItemUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Collection\SlotCreateStruct;
use Netgen\Layouts\API\Values\Collection\SlotUpdateStruct;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\Layouts\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\Layouts\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\Layouts\Persistence\Values\Collection\Slot as PersistenceSlot;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Ramsey\Uuid\Uuid;

final class CollectionServiceTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionType(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->method('loadCollection')
            ->willReturn(new PersistenceCollection());

        $this->collectionHandler
            ->method('deleteCollectionQuery')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->changeCollectionType(
            Collection::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT, 'query' => new Query()]),
            Collection::TYPE_MANUAL,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::addItem
     */
    public function testAddItem(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->method('loadCollection')
            ->willReturn(new PersistenceCollection());

        $this->collectionHandler
            ->method('addItem')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->definition = ItemDefinition::fromArray(['valueType' => 'value_type']);

        $this->collectionService->addItem(
            Collection::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]),
            $itemCreateStruct,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::addItem
     */
    public function testUpdateItem(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->method('loadItem')
            ->willReturn(PersistenceItem::fromArray(['config' => []]));

        $this->collectionHandler
            ->method('updateItem')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->updateItem(
            Item::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT, 'definition' => new ItemDefinition()]),
            new ItemUpdateStruct(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::moveItem
     */
    public function testMoveItem(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->method('loadItem')
            ->willReturn(new PersistenceItem());

        $this->collectionHandler
            ->method('moveItem')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->moveItem(Item::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]), 0);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::deleteItem
     */
    public function testDeleteItem(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->method('loadItem')
            ->willReturn(new PersistenceItem());

        $this->collectionHandler
            ->method('deleteItem')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteItem(Item::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::deleteItem
     */
    public function testDeleteItems(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->method('loadCollection')
            ->willReturn(new PersistenceCollection());

        $this->collectionHandler
            ->method('deleteItems')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteItems(Collection::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::updateQuery
     */
    public function testUpdateQuery(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $persistenceQuery = PersistenceQuery::fromArray(
            [
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'parameters' => ['en' => []],
            ],
        );

        $this->collectionHandler
            ->method('loadQuery')
            ->willReturn($persistenceQuery);

        $this->collectionHandler
            ->method('updateQueryTranslation')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $struct = new QueryUpdateStruct();
        $struct->locale = 'en';

        $this->collectionService->updateQuery(
            Query::fromArray(
                [
                    'id' => Uuid::uuid4(),
                    'status' => Value::STATUS_DRAFT,
                    'queryType' => new QueryType('type'),
                ],
            ),
            $struct,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::addSlot
     */
    public function testAddSlot(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->method('loadCollection')
            ->willReturn(new PersistenceCollection());

        $this->collectionHandler
            ->method('addSlot')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $slotCreateStruct = new SlotCreateStruct();
        $slotCreateStruct->viewType = 'my_view_type';

        $this->collectionService->addSlot(
            Collection::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]),
            $slotCreateStruct,
            1,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::addSlot
     */
    public function testUpdateSlot(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->method('loadSlot')
            ->willReturn(new PersistenceSlot());

        $this->collectionHandler
            ->method('updateSlot')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->updateSlot(
            Slot::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]),
            new SlotUpdateStruct(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::deleteSlot
     */
    public function testDeleteSlot(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->method('loadSlot')
            ->willReturn(new PersistenceSlot());

        $this->collectionHandler
            ->method('deleteSlot')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteSlot(Slot::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::deleteSlot
     */
    public function testDeleteSlots(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->method('loadCollection')
            ->willReturn(new PersistenceCollection());

        $this->collectionHandler
            ->method('deleteSlots')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteSlots(Collection::fromArray(['id' => Uuid::uuid4(), 'status' => Value::STATUS_DRAFT]));
    }
}
