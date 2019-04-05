<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Layout;

use Doctrine\Common\Collections\ArrayCollection;

final class LayoutList extends ArrayCollection
{
    public function __construct(array $layouts = [])
    {
        parent::__construct(
            array_filter(
                $layouts,
                static function (Layout $layout) {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    public function getLayouts(): array
    {
        return $this->toArray();
    }

    /**
     * @return int[]|string[]
     */
    public function getLayoutIds(): array
    {
        return array_map(
            static function (Layout $layout) {
                return $layout->getId();
            },
            $this->getLayouts()
        );
    }
}
