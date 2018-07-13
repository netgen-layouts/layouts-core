<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Stubs;

use Netgen\BlockManager\API\Values\Value as APIValue;
use Netgen\BlockManager\Core\Values\ValueStatusTrait;
use Netgen\BlockManager\Value as BaseValue;

final class Value extends BaseValue implements APIValue
{
    use ValueStatusTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $locale;

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
