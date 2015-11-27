<?php

namespace Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\LayoutResolver\Target;
use Symfony\Component\HttpFoundation\Request;

class PathInfo implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the unique identifier of the target this builder builds.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'path_info';
    }

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Target
     */
    public function buildTarget()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        return new Target(
            $this->getTargetIdentifier(),
            array($currentRequest->getPathInfo())
        );
    }
}
