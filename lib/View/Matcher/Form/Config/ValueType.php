<?php

namespace Netgen\BlockManager\View\Matcher\Form\Config;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\FormViewInterface;
use Netgen\BlockManager\View\ViewInterface;

class ValueType implements MatcherInterface
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

        if (!$view->getForm()->getConfig()->hasOption('configurable')) {
            return false;
        }

        $value = $view->getForm()->getConfig()->getOption('configurable');

        foreach ($config as $configItem) {
            if (is_a($value, $configItem, true)) {
                return true;
            }
        }

        return false;
    }
}
