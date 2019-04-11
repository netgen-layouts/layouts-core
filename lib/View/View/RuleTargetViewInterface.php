<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\ViewInterface;

interface RuleTargetViewInterface extends ViewInterface
{
    /**
     * Returns the target.
     */
    public function getTarget(): Target;
}
