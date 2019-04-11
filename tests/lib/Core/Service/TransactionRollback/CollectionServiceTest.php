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
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\Layouts\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\Layouts\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;

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
            ->expects(self::at(0))
            ->method('loadCollection')
            ->willReturn(new PersistenceCollection());

        $this->collectionHandler
            ->expects(self::at(1))
            ->method('deleteCollectionQuery')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->changeCollectionType(
            Collection::fromArray(['status' => Value::STATUS_DRAFT, 'query' => new Query()]),
            Collection::TYPE_MANUAL
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
            ->expects(self::at(0))
            ->method('loadCollection')
            ->willReturn(new PersistenceCollection());

        $this->collectionHandler
            ->expects(self::at(1))
            ->method('addItem')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->definition = ItemDefinition::fromArray(['valueType' => 'value_type']);

        $this->collectionService->addItem(
            Collection::fromArray(['status' => Value::STATUS_DRAFT]),
            $itemCreateStruct
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
            ->expects(self::at(0))
            ->method('loadItem')
            ->willReturn(PersistenceItem::fromArray(['config' => []]));

        $this->collectionHandler
            ->expects(self::at(1))
            ->method('updateItem')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->updateItem(
            Item::fromArray(['status' => Value::STATUS_DRAFT, 'definition' => new ItemDefinition()]),
            new ItemUpdateStruct()
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
            ->expects(self::at(0))
            ->method('loadItem')
            ->willReturn(new PersistenceItem());

        $this->collectionHandler
            ->expects(self::at(1))
            ->method('moveItem')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->moveItem(Item::fromArray(['status' => Value::STATUS_DRAFT]), 0);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::deleteItem
     */
    public function testDeleteItem(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->expects(self::at(0))
            ->method('loadItem')
            ->willReturn(new PersistenceItem());

        $this->collectionHandler
            ->expects(self::at(1))
            ->method('deleteItem')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteItem(Item::fromArray(['status' => Value::STATUS_DRAFT]));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\CollectionService::deleteItem
     */
    public function testDeleteItems(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->collectionHandler
            ->expects(self::at(0))
            ->method('loadCollection')
            ->willReturn(new PersistenceCollection());

        $this->collectionHandler
            ->expects(self::at(1))
            ->method('deleteItems')
            ->willThrowException(new Exception('Test exception text'));

        $this->transactionHandler
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->collectionService->deleteItems(Collection::fromArray(['status' => Value::STATUS_DRAFT]));
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
            ]
        );

        $this->collectionHandler
            ->expects(self::at(0))
            ->method('loadQuery')
            ->willReturn($persistenceQuery);

        $this->collectionHandler
            ->expects(self::at(1))
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
                    'status' => Value::STATUS_DRAFT,
                    'queryType' => new QueryType('type'),
                ]
            ),
            $struct
        );
    }
}
