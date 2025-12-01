<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer;

enum EntityType: string
{
    case Layout = 'layout';
    case RuleGroup = 'rule_group';
    case Rule = 'rule';
    case Role = 'role';
}
