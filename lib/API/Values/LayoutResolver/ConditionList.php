<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_map;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<int, \Netgen\Layouts\API\Values\LayoutResolver\Condition>
 */
final class ConditionList extends ArrayCollection
{
    /**
     * @param \Netgen\Layouts\API\Values\LayoutResolver\Condition[] $conditions
     */
    public function __construct(array $conditions = [])
    {
        parent::__construct(
            array_filter(
                $conditions,
                static fn (Condition $condition): bool => true,
            ),
        );
    }

    /**
     * @return \Netgen\Layouts\API\Values\LayoutResolver\Condition[]
     */
    public function getConditions(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getConditionIds(): array
    {
        return array_map(
            static fn (Condition $condition): UuidInterface => $condition->getId(),
            $this->getConditions(),
        );
    }
}
