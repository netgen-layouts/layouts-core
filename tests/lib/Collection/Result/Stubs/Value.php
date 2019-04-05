<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result\Stubs;

final class Value
{
    /**
     * @var int|null
     */
    private $value;

    public function __construct(?int $value)
    {
        $this->value = $value;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }
}
