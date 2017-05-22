<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\ValueObject;

class Collection extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $validItemTypes;

    /**
     * @var array
     */
    protected $validQueryTypes;

    /**
     * Returns the collection identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the valid query types.
     *
     * @return array
     */
    public function getValidQueryTypes()
    {
        return $this->validQueryTypes;
    }

    /**
     * Returns if the provided query type is valid.
     *
     * @param string $queryType
     *
     * @return bool
     */
    public function isValidQueryType($queryType)
    {
        if (!is_array($this->validQueryTypes)) {
            return true;
        }

        return in_array($queryType, $this->validQueryTypes, true);
    }

    /**
     * Returns the valid item types.
     *
     * @return array
     */
    public function getValidItemTypes()
    {
        return $this->validItemTypes;
    }

    /**
     * Returns if the provided item type is valid.
     *
     * @param string $itemType
     *
     * @return bool
     */
    public function isValidItemType($itemType)
    {
        if (!is_array($this->validItemTypes)) {
            return true;
        }

        return in_array($itemType, $this->validItemTypes, true);
    }
}
