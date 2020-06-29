<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Stubs;

use Netgen\Layouts\API\Values\Value as APIValue;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Value implements APIValue
{
    use HydratorTrait;
    use ValueStatusTrait;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $locale;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
