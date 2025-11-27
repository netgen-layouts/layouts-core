<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Utils\HydratorTrait;

use function in_array;
use function is_array;

final class Collection
{
    use HydratorTrait;

    /**
     * Returns the collection identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns the valid item types.
     *
     * If null, all item types are valid.
     *
     * @var string[]|null
     */
    public private(set) ?array $validItemTypes;

    /**
     * Returns the valid query types.
     *
     * If null, all query types are valid.
     *
     * @var string[]|null
     */
    public private(set) ?array $validQueryTypes;

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
}
