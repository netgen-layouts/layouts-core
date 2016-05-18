<?php

namespace Netgen\BlockManager\Layout\Resolver\ConditionMatcher\Matcher;

use Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class RouteParameter implements ConditionMatcherInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the unique identifier of the condition this matcher matches.
     *
     * @return string
     */
    public function getConditionIdentifier()
    {
        return 'route_parameter';
    }

    /**
     * Returns if this condition matches provided parameters.
     *
     * @param array $parameters
     *
     * @return bool
     */
    public function matches(array $parameters)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        if (empty($parameters['parameter_name']) || empty($parameters['parameter_values'])) {
            return false;
        }

        $routeParameters = $currentRequest->attributes->get('_route_params', array());
        if (!isset($routeParameters[$parameters['parameter_name']])) {
            return false;
        }

        return in_array(
            $routeParameters[$parameters['parameter_name']],
            $parameters['parameter_values']
        );
    }
}
