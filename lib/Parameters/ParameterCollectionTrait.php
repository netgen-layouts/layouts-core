<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;

trait ParameterCollectionTrait
{
    public private(set) ParameterList $parameters {
        get => new ParameterList($this->parameters->toArray());
    }

    final public function getParameter(string $parameterName): Parameter
    {
        return $this->parameters->get($parameterName) ??
            throw ParameterException::noParameter($parameterName);
    }

    final public function hasParameter(string $parameterName): bool
    {
        return $this->parameters->containsKey($parameterName);
    }
}
