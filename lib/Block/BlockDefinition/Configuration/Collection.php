<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Value;

final class Collection extends Value
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array|null
     */
    protected $validItemTypes;

    /**
     * @var array|null
     */
    protected $validQueryTypes;

    /**
     * Returns the collection identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the valid query types.
     *
     * If null, all query types are valid.
     *
     * @return array|null
     */
    public function getValidQueryTypes(): ?array
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
    public function isValidQueryType(string $queryType): bool
    {
        if (!is_array($this->validQueryTypes)) {
            return true;
        }

        return in_array($queryType, $this->validQueryTypes, true);
    }

    /**
     * Returns the valid item types.
     *
     * If null, all item types are valid.
     *
     * @return array|null
     */
    public function getValidItemTypes(): ?array
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
    public function isValidItemType(string $itemType): bool
    {
        if (!is_array($this->validItemTypes)) {
            return true;
        }

        return in_array($itemType, $this->validItemTypes, true);
    }
}
