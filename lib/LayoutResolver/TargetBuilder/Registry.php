<?php

namespace Netgen\BlockManager\LayoutResolver\TargetBuilder;

use InvalidArgumentException;

class Registry implements RegistryInterface
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface[]
     */
    protected $targetBuilders = array();

    /**
     * Adds the target builder to the registry.
     *
     * @param string $targetIdentifier
     * @param \Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface $targetBuilder
     */
    public function addTargetBuilder($targetIdentifier, TargetBuilderInterface $targetBuilder)
    {
        $this->targetBuilders[$targetIdentifier] = $targetBuilder;
    }

    /**
     * Returns the target builder from the registry.
     *
     * @param string $targetIdentifier
     *
     * @throws \InvalidArgumentException If target with provided target identifier does not exist
     *
     * @return \Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface
     */
    public function getTargetBuilder($targetIdentifier)
    {
        if (!isset($this->targetBuilders[$targetIdentifier])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Target builder with "%s" target identifier does not exist.',
                    $targetIdentifier
                )
            );
        }

        return $this->targetBuilders[$targetIdentifier];
    }

    /**
     * Returns all target builders from the registry.
     *
     * @return \Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface[]
     */
    public function getTargetBuilders()
    {
        return $this->targetBuilders;
    }
}
