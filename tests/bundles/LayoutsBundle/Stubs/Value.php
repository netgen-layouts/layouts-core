<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Stubs;

use Netgen\BlockManager\API\Values\Value as APIValue;
use Netgen\BlockManager\API\Values\ValueStatusTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Value implements APIValue
{
    use HydratorTrait;
    use ValueStatusTrait;

    /**
     * @var int|string
     */
    private $id;

    /**
     * @var string
     */
    private $locale;

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
