<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Item\ValueLoader;

use Netgen\Layouts\Item\ValueLoaderInterface;
use Netgen\Layouts\Tests\App\Item\TestValue;

final class TestValueTypeValueLoader implements ValueLoaderInterface
{
    public function load(int|string $id): TestValue
    {
        return new TestValue((int) $id);
    }

    public function loadByRemoteId(int|string $remoteId): TestValue
    {
        return new TestValue((int) $remoteId);
    }
}
