<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Form\Query;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\FormViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

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
