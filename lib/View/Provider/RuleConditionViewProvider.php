<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\View\RuleConditionView;

class RuleConditionViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($valueObject, array $parameters = array())
    {
        /** @var \Netgen\BlockManager\API\Values\LayoutResolver\Condition $valueObject */
        $ruleConditionView = new RuleConditionView($valueObject);

        return $ruleConditionView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function supports($valueObject)
    {
        return $valueObject instanceof Condition;
    }
}
