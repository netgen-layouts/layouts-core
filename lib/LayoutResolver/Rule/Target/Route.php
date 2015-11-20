<?php

namespace Netgen\BlockManager\LayoutResolver\Rule\Target;

use Netgen\BlockManager\LayoutResolver\Rule\Target;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class Route extends Target
{
    use RequestStackAwareTrait;

    /**
     * Returns if this target matches.
     *
     * @return bool
     */
    public function matches()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        // If the values are empty, we match on all routes
        if (empty($this->values)) {
            return true;
        }

        return in_array(
            $currentRequest->attributes->get('_route'),
            $this->values
        );
    }
}
