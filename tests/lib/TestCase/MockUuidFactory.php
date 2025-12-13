<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use ArrayIterator;
use Iterator;
use Symfony\Component\Uid\Exception\LogicException;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

class MockUuidFactory extends UuidFactory
{
    private Iterator $sequence;

    /**
     * @param string[] $uuids
     */
    public function __construct(array $uuids)
    {
        $this->sequence = new ArrayIterator($uuids);

        parent::__construct();
    }

    public function create(): Uuid
    {
        if (!$this->sequence->valid()) {
            throw new LogicException('No more UUIDs in sequence.');
        }

        $uuid = $this->sequence->current();
        $this->sequence->next();

        return Uuid::fromString($uuid);
    }
}
