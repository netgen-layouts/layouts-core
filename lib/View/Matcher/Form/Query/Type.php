<?php

namespace Netgen\BlockManager\View\Matcher\Form\Query;

use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\FormViewInterface;
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

        if (!$view->getForm()->getConfig()->hasOption('queryType')) {
            return false;
        }

        $queryType = $view->getForm()->getConfig()->getOption('queryType');
        if (!$queryType instanceof QueryTypeInterface) {
            return false;
        }

        return in_array($queryType->getType(), $config);
    }
}
