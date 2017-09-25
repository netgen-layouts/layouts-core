<?php

namespace Netgen\BlockManager\View\Matcher\Parameter;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\ParameterViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the parameter in the provided view
 * has a parameter type with the value specified in the configuration.
 */
final class Type implements MatcherInterface
{
    public function match(ViewInterface $view, array $config)
    {
        if (!$view instanceof ParameterViewInterface) {
            return false;
        }

        $parameterType = $view->getParameterValue()->getParameter()->getType();

        return in_array($parameterType->getIdentifier(), $config, true);
    }
}
