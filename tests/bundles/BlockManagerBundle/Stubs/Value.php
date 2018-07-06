<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Stubs;

use Netgen\BlockManager\Core\Values\Value as BaseValue;

final class Value extends BaseValue
{
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
