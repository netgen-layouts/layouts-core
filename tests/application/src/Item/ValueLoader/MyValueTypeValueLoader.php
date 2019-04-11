<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Item\ValueLoader;

use Netgen\Layouts\Item\ValueLoaderInterface;
use Netgen\Layouts\Tests\App\Item\Value;

final class MyValueTypeValueLoader implements ValueLoaderInterface
{
    public function load($id): ?object
    {
        return new Value((int) $id);
    }

    public function loadByRemoteId($remoteId): ?object
    {
        return new Value((int) $remoteId);
    }
}
