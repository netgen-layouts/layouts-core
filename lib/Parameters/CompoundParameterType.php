<?php

namespace Netgen\BlockManager\Parameters;

abstract class CompoundParameterType extends ParameterType implements CompoundParameterTypeInterface
{
    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }
}
