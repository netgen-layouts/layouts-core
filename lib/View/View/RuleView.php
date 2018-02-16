<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

final class RuleView extends View implements RuleViewInterface
{
    public function getRule()
    {
        return $this->parameters['rule'];
    }

    public function getIdentifier()
    {
        return 'rule_view';
    }

    public function jsonSerialize()
    {
        return array(
            'ruleId' => $this->getRule()->getId(),
        );
    }
}
