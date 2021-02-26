<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Stubs;

use Netgen\Layouts\API\Values\Value as APIValue;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Value implements APIValue
{
    use HydratorTrait;
    use ValueStatusTrait;

    public UuidInterface $id;

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
