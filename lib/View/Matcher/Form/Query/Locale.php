<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher\Form\Query;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\FormViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the form in the provided view
 * is used to edit the query with the locale equal to
 * value provided in the configuration.
 */
final class Locale implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof FormViewInterface) {
            return false;
        }

        if (!$view->getForm()->getConfig()->hasOption('query')) {
            return false;
        }

        $query = $view->getForm()->getConfig()->getOption('query');
        if (!$query instanceof Query) {
            return false;
        }

        return in_array($query->getLocale(), $config, true);
    }
}
