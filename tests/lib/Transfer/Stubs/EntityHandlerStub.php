<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Stubs;

use Netgen\Layouts\API\Values\Value as APIValue;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Transfer\EntityHandlerInterface;
use Symfony\Component\Uid\Uuid;

final class EntityHandlerStub implements EntityHandlerInterface
{
    public function loadEntity(Uuid $uuid): APIValue
    {
        return Value::fromArray(['id' => $uuid]);
    }

    public function entityExists(Uuid $uuid): bool
    {
        return false;
    }

    public function deleteEntity(Uuid $uuid): void {}

    public function importEntity(array $data, bool $keepUuid): APIValue
    {
        return Value::fromArray([]);
    }
}
