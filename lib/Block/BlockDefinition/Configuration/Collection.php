<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Utils\HydratorTrait;

use function in_array;
use function is_array;

final class Collection
{
    use HydratorTrait;

    private string $identifier;

    /**
     * @var string[]|null
     */
    private ?array $validItemTypes;

    /**
     * @var string[]|null
     */
    private ?array $validQueryTypes;

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
     *
     * @return string[]|null
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
     *
     * @return string[]|null
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
