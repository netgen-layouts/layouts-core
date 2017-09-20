<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\Core\Values\Collection\Query;

class Collection implements APICollection
{
    /**
     * @var array
     */
    private $manualItems;

    /**
     * @var array
     */
    private $overrideItems;

    /**
     * @var array
     */
    private $queryValues;

    /**
     * @var int
     */
    private $queryCount;

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
        array $queryValues = array(),
        $queryCount = 0
    ) {
        $this->manualItems = $manualItems;
        $this->overrideItems = $overrideItems;
        $this->queryValues = $queryValues;
        $this->queryCount = $queryCount;
    }

    /**
     * Returns the collection ID.
     *
     * @return int|string
     */
    public function getId()
    {
    }

    /**
     * Returns the collection status.
     *
     * @return int
     */
    public function getStatus()
    {
    }

    /**
     * Returns the collection type.
     *
     * @return int
     */
    public function getType()
    {
    }

    /**
     * Returns if the collection is published.
     *
     * @return bool
     */
    public function isPublished()
    {
    }

    /**
     * Returns all collection items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getItems()
    {
    }

    /**
     * Returns if the collection has a manual item at specified position.
     *
     * @param int $position
     *
     * @return bool
     */
    public function hasManualItem($position)
    {
        return isset($this->manualItems[$position]);
    }

    /**
     * Returns the manual item at specified position.
     *
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getManualItem($position)
    {
        return isset($this->manualItems[$position]) ?
            $this->manualItems[$position] :
            null;
    }

    /**
     * Returns the manual items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getManualItems()
    {
        return $this->manualItems;
    }

    /**
     * Returns if the collection has an override item at specified position.
     *
     * @param int $position
     *
     * @return bool
     */
    public function hasOverrideItem($position)
    {
        return isset($this->overrideItems[$position]);
    }

    /**
     * Returns the override item at specified position.
     *
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getOverrideItem($position)
    {
        return isset($this->overrideItems[$position]) ?
            $this->overrideItems[$position] :
            null;
    }

    /**
     * Returns the override items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getOverrideItems()
    {
        return $this->overrideItems;
    }

    /**
     * Returns the query from the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function getQuery()
    {
        return new Query(
            array(
                'queryType' => new QueryType(
                    'ezcontent_search',
                    $this->queryValues,
                    $this->queryCount
                ),
            )
        );
    }

    /**
     * Returns if the query exists in the collection.
     *
     * @return bool
     */
    public function hasQuery()
    {
        return true;
    }

    /**
     * Returns the list of all available locales in the collection.
     *
     * @return string[]
     */
    public function getAvailableLocales()
    {
    }

    /**
     * Returns the main locale for the collection.
     *
     * @return string
     */
    public function getMainLocale()
    {
    }

    /**
     * Returns if the collection has the provided locale.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function hasLocale($locale)
    {
    }

    /**
     * Returns if the collection is translatable.
     *
     * @return bool
     */
    public function isTranslatable()
    {
    }

    /**
     * Returns if the main translation of the collection is used
     * in case there are no prioritized translations.
     *
     * @return bool
     */
    public function isAlwaysAvailable()
    {
    }
}
