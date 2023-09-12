<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

abstract class CompoundParameterType extends ParameterType implements CompoundParameterTypeInterface
{
    public function buildParameters(ParameterBuilderInterface $builder): void {}
}
