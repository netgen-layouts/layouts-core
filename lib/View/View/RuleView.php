<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\View;

final class RuleView extends View implements RuleViewInterface
{
    public string $identifier {
        get => 'rule';
    }

    public function __construct(
        public private(set) Rule $rule,
    ) {
        $this->addInternalParameter('rule', $this->rule);
    }
}
