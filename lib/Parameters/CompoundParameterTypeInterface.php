<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

interface CompoundParameterTypeInterface extends ParameterTypeInterface
{
    /**
     * Builds the parameters by using provided parameter builder.
     */
    public function buildParameters(ParameterBuilderInterface $builder): void;
}
