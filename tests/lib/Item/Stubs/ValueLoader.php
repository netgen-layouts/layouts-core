<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item\Stubs;

use Netgen\BlockManager\Item\ValueLoaderInterface;

final class ValueLoader implements ValueLoaderInterface
{
    /**
     * @var bool
     */
    private $exists;

    public function __construct(bool $exists)
    {
        $this->exists = $exists;
    }

    public function load($id): ?object
    {
        return $this->exists ? new Value($id, '') : null;
    }

    public function loadByRemoteId($remoteId): ?object
    {
        return $this->exists ? new Value(0, $remoteId) : null;
    }
}
