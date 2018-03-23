<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\View\RuleView;

final class RuleViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = array())
    {
        return new RuleView(
            array(
                'rule' => $value,
            )
        );
    }

    public function supports($value)
    {
        return $value instanceof Rule;
    }
}
