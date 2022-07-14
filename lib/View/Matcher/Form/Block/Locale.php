<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Form\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\FormViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the form in the provided view
 * is used to edit the block with the locale equal to
 * value provided in the configuration.
 */
final class Locale implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof FormViewInterface) {
            return false;
        }

        if (!$view->getForm()->getConfig()->hasOption('block')) {
            return false;
        }

        $block = $view->getForm()->getConfig()->getOption('block');
        if (!$block instanceof Block) {
            return false;
        }

        return in_array($block->getLocale(), $config, true);
    }
}
