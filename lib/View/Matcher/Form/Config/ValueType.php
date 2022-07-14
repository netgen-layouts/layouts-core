<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Form\Config;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\FormViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function is_a;

/**
 * This matcher matches if the form in the provided view
 * is used to edit the config with the value type equal to
 * value provided in the configuration.
 */
final class ValueType implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
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
