<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LazyCollection;
use Symfony\Component\Uid\Uuid;

use function array_map;

/**
 * @extends \Netgen\Layouts\API\Values\LazyCollection<int, \Netgen\Layouts\API\Values\LayoutResolver\Condition>
 */
final class ConditionList extends LazyCollection
{
    /**
     * @return \Netgen\Layouts\API\Values\LayoutResolver\Condition[]
     */
    public function getConditions(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Symfony\Component\Uid\Uuid[]
     */
    public function getConditionIds(): array
    {
        return array_map(
            static fn (Condition $condition): Uuid => $condition->id,
            $this->getConditions(),
        );
    }
}
