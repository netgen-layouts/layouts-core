<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\View\View\RuleTargetView;

final class RuleTargetViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = array())
    {
        return new RuleTargetView(
            array(
                'target' => $value,
            )
        );
    }

    public function supports($value)
    {
        return $value instanceof Target;
    }
}
