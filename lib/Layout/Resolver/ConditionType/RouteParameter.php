<?php

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class RouteParameter implements ConditionTypeInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the condition type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'route_parameter';
    }

    /**
     * Returns if this condition matches the provided value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matches($value)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        if (!is_array($value)) {
            return false;
        }

        if (empty($value['parameter_name']) || empty($value['parameter_values'])) {
            return false;
        }

        $routeParameters = $currentRequest->attributes->get('_route_params', array());
        if (!isset($routeParameters[$value['parameter_name']])) {
            return false;
        }

        return in_array(
            $routeParameters[$value['parameter_name']],
            $value['parameter_values']
        );
    }
}
