<?php

namespace Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\LayoutResolver\Target\RequestUriPrefix as RequestUriPrefixTarget;
use Symfony\Component\HttpFoundation\Request;

class RequestUriPrefix implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

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

        return new RequestUriPrefixTarget(
            array($currentRequest->getRequestUri())
        );
    }
}
