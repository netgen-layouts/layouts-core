<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Stubs;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\Value as APIValue;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Value implements APIValue
{
    use HydratorTrait;
    use ValueStatusTrait;

    public private(set) UuidInterface $id;

    public private(set) Status $status;

    public private(set) string $locale;
}
