<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Parameter;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\ParameterViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the parameter in the provided view
 * has a parameter type with the value specified in the configuration.
 */
final class Type implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof ParameterViewInterface) {
            return false;
        }

        $parameterType = $view->getParameterValue()->getParameterDefinition()->getType();

        return in_array($parameterType::getIdentifier(), $config, true);
    }
}
