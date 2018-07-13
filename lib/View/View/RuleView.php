<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\View;

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
