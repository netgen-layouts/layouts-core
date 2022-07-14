<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Form\Query;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\NullQueryType;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\FormViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the form in the provided view
 * is used to edit the query with the query type equal to
 * value provided in the configuration.
 */
final class Type implements MatcherInterface
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

        $queryType = $query->getQueryType();
        if ($queryType instanceof NullQueryType) {
            return in_array('null', $config, true);
        }

        return in_array($queryType->getType(), $config, true);
    }
}
