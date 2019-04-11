<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\View;

final class RuleTargetView extends View implements RuleTargetViewInterface
{
    public function __construct(Target $target)
    {
        $this->parameters['target'] = $target;
    }

    public function getTarget(): Target
    {
        return $this->parameters['target'];
    }

    public static function getIdentifier(): string
    {
        return 'rule_target';
    }
}
