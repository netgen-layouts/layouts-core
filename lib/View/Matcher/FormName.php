<?php

namespace Netgen\BlockManager\View\Matcher;

use Netgen\BlockManager\View\ViewInterface;

class FormName implements MatcherInterface
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
        if (!$view->hasParameter('form_name')) {
            return false;
        }

        return in_array($view->getParameter('form_name'), $config);
    }
}
