<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder;

use Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\Layout\Resolver\Target\PathInfo as PathInfoTarget;
use Symfony\Component\HttpFoundation\Request;

class PathInfo implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Target
     */
    public function buildTarget()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        return new PathInfoTarget(
            array($currentRequest->getPathInfo())
        );
    }
}
