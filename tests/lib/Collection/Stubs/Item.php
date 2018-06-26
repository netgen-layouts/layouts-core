<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use DateTimeInterface;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Core\Values\Value;
use Netgen\BlockManager\Item\CmsItemInterface;

final class Item extends Value implements APIItem
{
    use ConfigAwareValueTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $collectionId;

    /**
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    protected $definition;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int|string
     */
    protected $value;

    /**
     * @var \Netgen\BlockManager\Item\CmsItemInterface
     */
    protected $cmsItem;

    /**
     * @var bool
     */
    protected $isValid;

    public function getId()
    {
        return $this->id;
    }

    public function getCollectionId()
    {
        return $this->collectionId;
    }

    public function isManual(): bool
    {
        return $this->type === self::TYPE_MANUAL;
    }

    public function isOverride(): bool
    {
        return $this->type === self::TYPE_OVERRIDE;
    }

    public function getDefinition(): ItemDefinitionInterface
    {
        return $this->definition;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getCmsItem(): CmsItemInterface
    {
        return $this->cmsItem;
    }

    public function isScheduled(): bool
    {
        return false;
    }

    public function isVisible(?DateTimeInterface $reference = null): bool
    {
        return true;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }
}
