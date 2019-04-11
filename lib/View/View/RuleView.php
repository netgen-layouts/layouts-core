<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\View;

final class RuleView extends View implements RuleViewInterface
{
    public function __construct(Rule $rule)
    {
        $this->parameters['rule'] = $rule;
    }

    public function getRule(): Rule
    {
        return $this->parameters['rule'];
    }

    public static function getIdentifier(): string
    {
        return 'rule';
    }
}
