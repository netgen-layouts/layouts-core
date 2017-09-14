<?php

namespace Netgen\BlockManager\Parameters;

abstract class CompoundParameterType extends ParameterType implements CompoundParameterTypeInterface
{
    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }
}
