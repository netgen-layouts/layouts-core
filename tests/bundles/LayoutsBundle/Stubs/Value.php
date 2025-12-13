<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Stubs;

use Netgen\Layouts\API\Values\Value as APIValue;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Symfony\Component\Uid\Uuid;

final class Value implements APIValue
{
    use HydratorTrait;
    use ValueStatusTrait;

    public private(set) Uuid $id;

    public private(set) string $locale;
}
