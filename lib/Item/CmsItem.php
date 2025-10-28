<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

use Netgen\Layouts\Utils\HydratorTrait;

final class CmsItem implements CmsItemInterface
{
    use HydratorTrait;

    private int|string $value;

    private int|string $remoteId;

    private string $valueType;

    private string $name;

    private bool $isVisible;

    private ?object $object;

    public function getValue(): int|string
    {
        return $this->value;
    }

    public function getRemoteId(): int|string
    {
        return $this->remoteId;
    }

    public function getValueType(): string
    {
        return $this->valueType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function getObject(): ?object
    {
        return $this->object;
    }
}
