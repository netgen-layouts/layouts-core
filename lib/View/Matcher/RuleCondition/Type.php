<?php

namespace Netgen\BlockManager\View\Matcher\RuleCondition;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\RuleConditionViewInterface;
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
        if (!$view instanceof RuleConditionViewInterface) {
            return false;
        }

        return in_array($view->getCondition()->getType(), $config);
    }
}
