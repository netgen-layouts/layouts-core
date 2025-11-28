<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\View\View;

final class RuleConditionView extends View implements RuleConditionViewInterface
{
    public string $identifier {
        get => 'rule_condition';
    }

    public Condition $condition {
        get => $this->getParameter('condition');
    }

    public function __construct(Condition $condition)
    {
        $this->addInternalParameter('condition', $condition);
    }
}
