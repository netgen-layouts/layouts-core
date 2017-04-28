<?php

namespace Netgen\BlockManager\Parameters\Registry;

use Netgen\BlockManager\Parameters\Form\MapperInterface;

interface FormMapperRegistryInterface
{
    /**
     * Adds a parameter form mapper to registry.
     *
     * @param string $parameterType
     * @param \Netgen\BlockManager\Parameters\Form\MapperInterface $formMapper
     */
    public function addFormMapper($parameterType, MapperInterface $formMapper);

    /**
     * Returns if registry has a parameter form mapper.
     *
     * @param string $parameterType
     *
     * @return bool
     */
    public function hasFormMapper($parameterType);

    /**
     * Returns a form mapper for provided parameter type.
     *
     * @param string $parameterType
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterTypeException If form mapper does not exist
     *
     * @return \Netgen\BlockManager\Parameters\Form\MapperInterface
     */
    public function getFormMapper($parameterType);

    /**
     * Returns all form mappers.
     *
     * @return \Netgen\BlockManager\Parameters\Form\MapperInterface[]
     */
    public function getFormMappers();
}
