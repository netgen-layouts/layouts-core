<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_map;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<int, \Netgen\Layouts\API\Values\Layout\Layout>
 */
final class LayoutList extends ArrayCollection
{
    /**
     * @param \Netgen\Layouts\API\Values\Layout\Layout[] $layouts
     */
    public function __construct(array $layouts = [])
    {
        parent::__construct(
            array_filter(
                $layouts,
                static fn (Layout $layout): bool => true,
            ),
        );
    }

    /**
     * @return \Netgen\Layouts\API\Values\Layout\Layout[]
     */
    public function getLayouts(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getLayoutIds(): array
    {
        return array_map(
            static fn (Layout $layout): UuidInterface => $layout->getId(),
            $this->getLayouts(),
        );
    }
}
