<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetBuilder;

interface RegistryInterface
{
    /**
     * Adds the target builder to the registry.
     *
     * @param string $targetIdentifier
     * @param \Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface $targetBuilder
     */
    public function addTargetBuilder($targetIdentifier, TargetBuilderInterface $targetBuilder);

    /**
     * Returns the target builder from the registry.
     *
     * @param string $targetIdentifier
     *
     * @throws \InvalidArgumentException If target builder with provided target identifier does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface
     */
    public function getTargetBuilder($targetIdentifier);

    /**
     * Returns all target builders from the registry.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface[]
     */
    public function getTargetBuilders();
}
