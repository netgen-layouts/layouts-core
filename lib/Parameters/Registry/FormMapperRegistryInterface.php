<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Parameters\Form\MapperInterface;

interface FormMapperRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a parameter form mapper to registry.
     */
    public function addFormMapper(string $parameterType, MapperInterface $formMapper): void;

    /**
     * Returns if registry has a parameter form mapper.
     */
    public function hasFormMapper(string $parameterType): bool;

    /**
     * Returns a form mapper for provided parameter type.
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterTypeException If form mapper does not exist
     */
    public function getFormMapper(string $parameterType): MapperInterface;

    /**
     * Returns all form mappers.
     *
     * @return \Netgen\BlockManager\Parameters\Form\MapperInterface[]
     */
    public function getFormMappers(): array;
}
