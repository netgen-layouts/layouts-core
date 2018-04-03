<?php

namespace Netgen\BlockManager\Core\Service\Mapper\Proxy;

trait LazyLoadingProxyTrait
{
    /**
     * Lazily loads the provided variable, meaning, if it is callable,
     * it executes it and stores the result of execution into itself,
     * making it lazily loaded for the next run.
     *
     * @param mixed $property
     *
     * @return mixed
     */
    private function getLazyLoadedProperty(&$property)
    {
        if (is_callable($property)) {
            $property = $property();
        }

        return $property;
    }
}
