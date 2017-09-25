<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\View\RuleView;

final class RuleViewProvider implements ViewProviderInterface
{
    public function provideView($valueObject, array $parameters = array())
    {
        return new RuleView(
            array(
                'rule' => $valueObject,
            )
        );
    }

    public function supports($valueObject)
    {
        return $valueObject instanceof Rule;
    }
}
