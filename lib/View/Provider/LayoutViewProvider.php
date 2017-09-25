<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\View\View\LayoutView;

final class LayoutViewProvider implements ViewProviderInterface
{
    public function provideView($valueObject, array $parameters = array())
    {
        return new LayoutView(
            array(
                'layout' => $valueObject,
            )
        );
    }

    public function supports($valueObject)
    {
        return $valueObject instanceof Layout;
    }
}
