<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

/**
 * Container definition represents the model of the container block,
 * built from configuration. In addition to all features from block definition,
 * this model specifies which placeholders the container block has.
 */
interface ContainerDefinitionInterface extends BlockDefinitionInterface
{
    /**
     * Returns all placeholder identifiers in this definition.
     *
     * @return string[]
     */
    public function getPlaceholders(): array;
}
