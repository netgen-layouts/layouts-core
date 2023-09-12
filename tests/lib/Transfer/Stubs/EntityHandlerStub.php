<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Stubs;

use Netgen\Layouts\API\Values\Value as APIValue;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Transfer\EntityHandlerInterface;
use Ramsey\Uuid\UuidInterface;

final class EntityHandlerStub implements EntityHandlerInterface
{
    public function loadEntity(UuidInterface $uuid): APIValue
    {
        return Value::fromArray(['id' => $uuid]);
    }

    public function entityExists(UuidInterface $uuid): bool
    {
        return false;
    }

    public function deleteEntity(UuidInterface $uuid): void {}

    public function importEntity(array $data, bool $keepUuid): APIValue
    {
        return Value::fromArray([]);
    }
}
