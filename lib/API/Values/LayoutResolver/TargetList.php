<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LazyCollection;
use Ramsey\Uuid\UuidInterface;

use function array_map;

/**
 * @extends \Netgen\Layouts\API\Values\LazyCollection<int, \Netgen\Layouts\API\Values\LayoutResolver\Target>
 */
final class TargetList extends LazyCollection
{
    /**
     * @return \Netgen\Layouts\API\Values\LayoutResolver\Target[]
     */
    public function getTargets(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getTargetIds(): array
    {
        return array_map(
            static fn (Target $target): UuidInterface => $target->id,
            $this->getTargets(),
        );
    }
}
