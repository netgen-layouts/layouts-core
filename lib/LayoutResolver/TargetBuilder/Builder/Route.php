<?php

namespace Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\LayoutResolver\Target\Route as RouteTarget;
use Symfony\Component\HttpFoundation\Request;

class Route implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\LayoutResolver\TargetInterface
     */
    public function buildTarget()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        return new RouteTarget(
            array($currentRequest->attributes->get('_route'))
        );
    }
}
