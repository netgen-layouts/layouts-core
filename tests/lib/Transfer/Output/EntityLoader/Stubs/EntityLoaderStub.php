<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\EntityLoader\Stubs;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Transfer\Output\EntityLoaderInterface;

final class EntityLoaderStub implements EntityLoaderInterface
{
    public function loadEntities(array $entityIds): iterable
    {
        foreach ($entityIds as $entityId) {
            yield Value::fromArray(['id' => $entityId]);
        }
    }
}
