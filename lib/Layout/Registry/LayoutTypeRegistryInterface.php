<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;

interface LayoutTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a layout type.
     */
    public function hasLayoutType(string $identifier): bool;

    /**
     * Returns the layout type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Layout\LayoutTypeException If layout type with provided identifier does not exist
     */
    public function getLayoutType(string $identifier): LayoutTypeInterface;

    /**
     * Returns all layout types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\Layouts\Layout\Type\LayoutTypeInterface[]
     */
    public function getLayoutTypes(bool $onlyEnabled = false): array;
}
