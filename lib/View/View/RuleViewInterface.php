<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\ViewInterface;

interface RuleViewInterface extends ViewInterface
{
    /**
     * Returns the rule.
     */
    public function getRule(): Rule;
}
