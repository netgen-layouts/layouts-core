<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder;

use Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\Layout\Resolver\Target\RoutePrefix as RoutePrefixTarget;
use Symfony\Component\HttpFoundation\Request;

class RoutePrefix implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetInterface
     */
    public function buildTarget()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        return new RoutePrefixTarget(
            array($currentRequest->attributes->get('_route'))
        );
    }
}
