<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\View\View\RuleConditionView;

final class RuleConditionViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = array())
    {
        return new RuleConditionView(
            array(
                'condition' => $value,
            )
        );
    }

    public function supports($value)
    {
        return $value instanceof Condition;
    }
}
