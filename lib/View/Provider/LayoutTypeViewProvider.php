<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\View\View\LayoutTypeView;

final class LayoutTypeViewProvider implements ViewProviderInterface
{
    public function provideView($valueObject, array $parameters = array())
    {
        return new LayoutTypeView(
            array(
                'layoutType' => $valueObject,
            )
        );
    }

    public function supports($valueObject)
    {
        return $valueObject instanceof LayoutType;
    }
}
