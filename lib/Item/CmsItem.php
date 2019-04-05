<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Utils\HydratorTrait;

final class CmsItem implements CmsItemInterface
{
    use HydratorTrait;

    /**
     * @var int|string
     */
    private $value;

    /**
     * @var int|string
     */
    private $remoteId;

    /**
     * @var string
     */
    private $valueType;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isVisible;

    /**
     * @var mixed
     */
    private $object;

    public function getValue()
    {
        return $this->value;
    }

    public function getRemoteId()
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
