<?php

namespace Netgen\BlockManager\View\Matcher\Form\Query;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\FormViewInterface;
use Netgen\BlockManager\View\ViewInterface;

class Type implements MatcherInterface
{
    /**
     * Returns if the view matches the config.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param array $config
     *
     * @return bool
     */
    public function match(ViewInterface $view, array $config)
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

        return in_array($query->getQueryType()->getType(), $config);
    }
}
