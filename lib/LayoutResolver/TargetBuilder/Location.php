<?php

namespace Netgen\BlockManager\LayoutResolver\TargetBuilder;

use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\LayoutResolver\Target;
use Symfony\Component\HttpFoundation\Request;

class Location implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the unique identifier of the target this builder builds.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'location';
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

        if (!$currentRequest->attributes->has('locationId')) {
            return false;
        }

        return new Target(
            $this->getTargetIdentifier(),
            array($currentRequest->attributes->get('locationId'))
        );
    }
}
