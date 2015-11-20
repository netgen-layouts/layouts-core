<?php

namespace Netgen\BlockManager\LayoutResolver\Rule\Condition;

use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\LayoutResolver\Rule\Condition;
use Netgen\BlockManager\LayoutResolver\Rule\Target\Route;
use Netgen\BlockManager\LayoutResolver\Rule\TargetInterface;
use Symfony\Component\HttpFoundation\Request;

class RouteParameter extends Condition
{
    use RequestStackAwareTrait;

    /**
     * Returns if this condition matches.
     *
     * @return bool
     */
    public function matches()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        if (empty($this->values)) {
            return false;
        }

        if (empty($this->identifier)) {
            return false;
        }

        $routeParameters = $currentRequest->attributes->get('_route_params', array());
        if (!isset($routeParameters[$this->identifier])) {
            return false;
        }

        return in_array($routeParameters[$this->identifier], $this->values);
    }

    /**
     * Returns if this condition supports the given target.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface
     *
     * @return bool
     */
    public function supports(TargetInterface $target)
    {
        return $target instanceof Route;
    }
}
