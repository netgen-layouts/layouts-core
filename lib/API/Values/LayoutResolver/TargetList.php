<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_map;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<int, \Netgen\Layouts\API\Values\LayoutResolver\Target>
 */
final class TargetList extends ArrayCollection
{
    /**
     * @param \Netgen\Layouts\API\Values\LayoutResolver\Target[] $targets
     */
    public function __construct(array $targets = [])
    {
        parent::__construct(
            array_filter(
                $targets,
                static fn (Target $target): bool => true,
            ),
        );
    }

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
            static fn (Target $target): UuidInterface => $target->getId(),
            $this->getTargets(),
        );
    }
}
