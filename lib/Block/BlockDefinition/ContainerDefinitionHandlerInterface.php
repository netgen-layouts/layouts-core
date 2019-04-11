<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition;

/**
 * Container definition handler represents the dynamic/runtime part of the
 * Container definition.
 *
 * Implement this interface to create your own custom container blocks.
 */
interface ContainerDefinitionHandlerInterface extends BlockDefinitionHandlerInterface
{
    /**
     * Returns all placeholder identifiers for this container definition.
     *
     * @return string[]
     */
    public function getPlaceholderIdentifiers(): array;
}
