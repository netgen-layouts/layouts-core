<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\View\View\RuleTargetView;

final class RuleTargetViewProvider implements ViewProviderInterface
{
    public function provideView($valueObject, array $parameters = array())
    {
        return new RuleTargetView(
            array(
                'target' => $valueObject,
            )
        );
    }

    public function supports($valueObject)
    {
        return $valueObject instanceof Target;
    }
}
