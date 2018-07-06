<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Core\Values\LazyPropertyTrait;
use Netgen\BlockManager\Core\Values\Value;
use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\Item\NullCmsItem;

final class Item extends Value implements APIItem
{
    use ConfigAwareValueTrait;
    use LazyPropertyTrait;

    /**
     * @var int|string
     */
    private $id;

    /**
     * @var int|string
     */
    private $collectionId;

    /**
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    private $definition;

    /**
     * @var int
     */
    private $position;

    /**
     * @var int
     */
    private $type;

    /**
     * @var int|string
     */
    private $value;

    /**
     * @var \Netgen\BlockManager\Item\CmsItemInterface
     */
    private $cmsItem;

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
        return $this->getLazyProperty($this->cmsItem);
    }

    public function isValid(): bool
    {
        if ($this->getCmsItem() instanceof NullCmsItem) {
            return false;
        }

        return $this->getCmsItem()->isVisible();
    }
}
