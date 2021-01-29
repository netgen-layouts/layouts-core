<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\View\View;

final class RuleConditionView extends View implements RuleConditionViewInterface
{
    public function __construct(RuleCondition $condition)
    {
        $this->parameters['condition'] = $condition;
    }

    public function getCondition(): RuleCondition
    {
        return $this->parameters['condition'];
    }

    public static function getIdentifier(): string
    {
        return 'rule_condition';
    }
}
