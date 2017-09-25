<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\View\View\RuleConditionView;

final class RuleConditionViewProvider implements ViewProviderInterface
{
    public function provideView($valueObject, array $parameters = array())
    {
        return new RuleConditionView(
            array(
                'condition' => $valueObject,
            )
        );
    }

    public function supports($valueObject)
    {
        return $valueObject instanceof Condition;
    }
}
