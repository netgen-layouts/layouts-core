<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;

final class TargetList extends ArrayCollection
{
    public function __construct(array $targets = [])
    {
        parent::__construct(
            array_filter(
                $targets,
                static function (Target $target) {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target[]
     */
    public function getTargets(): array
    {
        return $this->toArray();
    }

    /**
     * @return int[]|string[]
     */
    public function getTargetIds(): array
    {
        return array_map(
            static function (Target $target) {
                return $target->getId();
            },
            $this->getTargets()
        );
    }
}
