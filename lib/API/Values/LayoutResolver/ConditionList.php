<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;

final class ConditionList extends ArrayCollection
{
    public function __construct(array $conditions = [])
    {
        parent::__construct(
            array_filter(
                $conditions,
                static function (Condition $condition): bool {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition[]
     */
    public function getConditions(): array
    {
        return $this->toArray();
    }

    /**
     * @return int[]|string[]
     */
    public function getConditionIds(): array
    {
        return array_map(
            static function (Condition $condition) {
                return $condition->getId();
            },
            $this->getConditions()
        );
    }
}
