<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;

use function array_filter;
use function array_map;
use function array_values;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<string, \Netgen\Layouts\API\Values\Block\Placeholder>
 */
final class PlaceholderList extends ArrayCollection
{
    /**
     * @param array<string, \Netgen\Layouts\API\Values\Block\Placeholder> $placeholders
     */
    public function __construct(array $placeholders = [])
    {
        parent::__construct(
            array_filter(
                $placeholders,
                static fn (Placeholder $placeholder): bool => true,
            ),
        );
    }

    /**
     * @return array<string, \Netgen\Layouts\API\Values\Block\Placeholder>
     */
    public function getPlaceholders(): array
    {
        return $this->toArray();
    }

    /**
     * @return string[]
     */
    public function getPlaceholderIdentifiers(): array
    {
        return array_values(
            array_map(
                static fn (Placeholder $placeholder): string => $placeholder->getIdentifier(),
                $this->getPlaceholders(),
            ),
        );
    }
}
