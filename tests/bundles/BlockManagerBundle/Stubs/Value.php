<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Stubs;

use Netgen\BlockManager\Core\Values\Value as BaseValue;

final class Value extends BaseValue
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $locale;

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
