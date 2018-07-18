<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Utils\HydratorTrait;

final class Collection
{
    use HydratorTrait;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var array|null
     */
    private $validItemTypes;

    /**
     * @var array|null
     */
    private $validQueryTypes;

    /**
     * Returns the collection identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the valid query types.
     *
     * If null, all query types are valid.
     */
    public function getValidQueryTypes(): ?array
    {
        return $this->validQueryTypes;
    }

    /**
     * Returns if the provided query type is valid.
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
     */
    public function getValidItemTypes(): ?array
    {
        return $this->validItemTypes;
    }

    /**
     * Returns if the provided item type is valid.
     */
    public function isValidItemType(string $itemType): bool
    {
        if (!is_array($this->validItemTypes)) {
            return true;
        }

        return in_array($itemType, $this->validItemTypes, true);
    }
}
