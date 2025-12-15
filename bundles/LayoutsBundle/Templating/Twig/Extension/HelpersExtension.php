<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class HelpersExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'nglayouts_layout_name',
                [HelpersRuntime::class, 'getLayoutName'],
            ),
            new TwigFunction(
                'nglayouts_value_type_name',
                [HelpersRuntime::class, 'getValueTypeName'],
            ),
            new TwigFunction(
                'nglayouts_rule_group',
                [HelpersRuntime::class, 'getRuleGroup'],
            ),
            new TwigFunction(
                'nglayouts_rule_group_name',
                [HelpersRuntime::class, 'getRuleGroupName'],
            ),
            new TwigFunction(
                'nglayouts_parent_rule_groups',
                [HelpersRuntime::class, 'getParentRuleGroups'],
            ),
        ];
    }
}
