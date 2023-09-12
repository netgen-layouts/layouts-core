<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\CollectionVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Collection\Collection>
 */
abstract class CollectionVisitorTestBase extends VisitorTestBase
{
    public function getVisitor(): VisitorInterface
    {
        return new CollectionVisitor();
    }

    public static function acceptDataProvider(): iterable
    {
        return [
            [new Collection(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public static function visitDataProvider(): iterable
    {
        return [
            [fn (): Collection => $this->collectionService->loadCollection(Uuid::fromString('45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a')), 'collection/collection_2.json'],
            [fn (): Collection => $this->collectionService->loadCollection(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89')), 'collection/collection_3.json'],
            [fn (): Collection => $this->collectionService->loadCollection(Uuid::fromString('00872ad1-60e2-5947-95c2-e2eb75427af6')), 'collection/collection_6.json'],
        ];
    }
}
