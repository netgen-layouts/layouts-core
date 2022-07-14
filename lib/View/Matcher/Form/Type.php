<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Form;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\FormViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the form in the provided view
 * has a form type with the value specified in the configuration.
 */
final class Type implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof FormViewInterface) {
            return false;
        }

        return in_array($view->getFormType(), $config, true);
    }
}
