<?php

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface;
use RuntimeException;

class TargetBuilderRegistry implements TargetBuilderRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface[]
     */
    protected $targetBuilders = array();

    /**
     * Adds the target builder to the registry.
     *
     * @param string $targetIdentifier
     * @param \Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface $targetBuilder
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
     * @throws \RuntimeException If target with provided target identifier does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface
     */
    public function getTargetBuilder($targetIdentifier)
    {
        if (!isset($this->targetBuilders[$targetIdentifier])) {
            throw new RuntimeException(
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
     * @return \Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface[]
     */
    public function getTargetBuilders()
    {
        return $this->targetBuilders;
    }
}
