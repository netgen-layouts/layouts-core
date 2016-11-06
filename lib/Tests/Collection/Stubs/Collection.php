<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\Core\Values\Collection\Query;

class Collection implements APICollection
{
    /**
     * @var array
     */
    protected $manualItems;

    /**
     * @var array
     */
    protected $overrideItems;

    /**
     * @var array
     */
    protected $queryValues;

    /**
     * @var array
     */
    protected $queryCounts;

    /**
     * Constructor.
     *
     * @param array $manualItems
     * @param array $overrideItems
     * @param array $queryValues
     * @param array $queryCounts
     */
    public function __construct(
        array $manualItems = array(),
        array $overrideItems = array(),
        array $queryValues = array(),
        array $queryCounts = array()
    ) {
        $this->manualItems = $manualItems;
        $this->overrideItems = $overrideItems;
        $this->queryValues = $queryValues;
        $this->queryCounts = $queryCounts;
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
     * Returns if the collection is shared.
     *
     * @return bool
     */
    public function isShared()
    {
    }

    /**
     * Returns the collection name.
     *
     * @return string
     */
    public function getName()
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
     * Returns the list of query configurations in the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query[]
     */
    public function getQueries()
    {
        $queries = array();

        foreach ($this->queryValues as $index => $singleQueryValues) {
            $queries[] = new Query(
                array(
                    'identifier' => $index,
                    'queryType' => new QueryType(
                        'ezcontent_search',
                        $singleQueryValues,
                        isset($this->queryCounts[$index]) ? $this->queryCounts[$index] : null
                    ),
                )
            );
        }

        return $queries;
    }
}
