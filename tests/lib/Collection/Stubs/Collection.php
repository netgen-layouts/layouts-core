<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Item\Item as CmsItem;

final class Collection implements APICollection
{
    /**
     * @var array
     */
    private $manualItems = array();

    /**
     * @var array
     */
    private $overrideItems = array();

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    private $query;

    /**
     * Constructor.
     *
     * @param array $manualItems
     * @param array $overrideItems
     * @param array $queryValues
     * @param int $queryCount
     */
    public function __construct(
        array $manualItems = array(),
        array $overrideItems = array(),
        array $queryValues = null,
        $queryCount = 0
    ) {
        foreach ($manualItems as $position => $value) {
            $this->manualItems[$position] = new Item(
                array(
                    'type' => Item::TYPE_MANUAL,
                    'value' => $value,
                    'cmsItem' => new CmsItem(array('value' => $value)),
                    'position' => $position,
                    'isValid' => $value !== null,
                )
            );
        }

        foreach ($overrideItems as $position => $value) {
            $this->overrideItems[$position] = new Item(
                array(
                    'type' => Item::TYPE_OVERRIDE,
                    'value' => $value,
                    'cmsItem' => new CmsItem(array('value' => $value)),
                    'position' => $position,
                    'isValid' => $value !== null,
                )
            );
        }

        if ($queryValues !== null) {
            $this->query = new Query(
                array(
                    'queryType' => new QueryType(
                        'ezcontent_search',
                        $queryValues,
                        $queryCount
                    ),
                )
            );
        }
    }

    public function getId()
    {
    }

    public function getStatus()
    {
    }

    public function getType()
    {
    }

    public function isPublished()
    {
    }

    public function getOffset()
    {
    }

    public function getLimit()
    {
    }

    public function hasItem($position, $type = null)
    {
        return $this->hasManualItem($position) || $this->hasOverrideItem($position);
    }

    public function getItem($position, $type = null)
    {
        $item = $this->getManualItem($position);
        if ($item !== null) {
            return $item;
        }

        return $this->getOverrideItem($position);
    }

    public function getItems()
    {
        $items = $this->manualItems + $this->overrideItems;

        ksort($items);

        return $items;
    }

    public function hasManualItem($position)
    {
        return isset($this->manualItems[$position]);
    }

    public function getManualItem($position)
    {
        return isset($this->manualItems[$position]) ?
            $this->manualItems[$position] :
            null;
    }

    public function getManualItems()
    {
        return $this->manualItems;
    }

    public function hasOverrideItem($position)
    {
        return isset($this->overrideItems[$position]);
    }

    public function getOverrideItem($position)
    {
        return isset($this->overrideItems[$position]) ?
            $this->overrideItems[$position] :
            null;
    }

    public function getOverrideItems()
    {
        return $this->overrideItems;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function hasQuery()
    {
        return $this->query instanceof APIQuery;
    }

    public function getAvailableLocales()
    {
    }

    public function getMainLocale()
    {
    }

    public function isTranslatable()
    {
    }

    public function isAlwaysAvailable()
    {
    }

    public function getLocale()
    {
    }
}
