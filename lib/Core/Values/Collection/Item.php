<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use DateTimeInterface;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Core\Values\LazyPropertyTrait;
use Netgen\BlockManager\Core\Values\Value;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Utils\DateTimeUtils;

final class Item extends Value implements APIItem
{
    use ConfigAwareValueTrait;
    use LazyPropertyTrait;

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
     * @var string
     */
    protected $valueType;

    /**
     * @var \Netgen\BlockManager\Item\ItemInterface
     */
    protected $cmsItem;

    public function getId()
    {
        return $this->id;
    }

    public function getCollectionId()
    {
        return $this->collectionId;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValueType()
    {
        return $this->valueType;
    }

    public function getCmsItem()
    {
        return $this->getLazyProperty($this->cmsItem);
    }

    public function isScheduled()
    {
        if (!$this->hasConfig('visibility')) {
            return false;
        }

        $visibilityConfig = $this->getConfig('visibility');

        return $visibilityConfig->getParameter('visibility_status')->getValue() === self::VISIBILITY_SCHEDULED;
    }

    public function isVisible(DateTimeInterface $reference = null)
    {
        if (!$this->hasConfig('visibility')) {
            return true;
        }

        $visibilityConfig = $this->getConfig('visibility');
        $visibilityStatus = $visibilityConfig->getParameter('visibility_status')->getValue();

        if ($visibilityStatus !== self::VISIBILITY_SCHEDULED) {
            return $visibilityStatus !== self::VISIBILITY_HIDDEN;
        }

        $visibleFrom = $visibilityConfig->getParameter('visible_from')->getValue();
        $visibleTo = $visibilityConfig->getParameter('visible_to')->getValue();

        return DateTimeUtils::isBetweenDates($reference, $visibleFrom, $visibleTo);
    }

    public function isValid()
    {
        if ($this->getCmsItem() instanceof NullItem) {
            return false;
        }

        return $this->isVisible() && $this->getCmsItem()->isVisible();
    }
}
