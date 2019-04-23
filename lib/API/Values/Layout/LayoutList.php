<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use Doctrine\Common\Collections\ArrayCollection;

final class LayoutList extends ArrayCollection
{
    public function __construct(array $layouts = [])
    {
        parent::__construct(
            array_filter(
                $layouts,
                static function (Layout $layout): bool {
                    return true;
                }
            )
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
            static function (Layout $layout) {
                return $layout->getId();
            },
            $this->getLayouts()
        );
    }
}
