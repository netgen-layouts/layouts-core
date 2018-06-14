<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher\Form;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\FormViewInterface;
use Netgen\BlockManager\View\ViewInterface;

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
