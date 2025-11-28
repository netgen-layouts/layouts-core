<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\View;

final class RuleTargetView extends View implements RuleTargetViewInterface
{
    public string $identifier {
        get => 'rule_target';
    }

    public Target $target {
        get => $this->getParameter('target');
    }

    public function __construct(Target $target)
    {
        $this->addInternalParameter('target', $target);
    }
}
