<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\View\QueryView;

class QueryViewProvider implements ViewProviderInterface
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
        /** @var \Netgen\BlockManager\API\Values\Collection\Query $valueObject */
        $queryView = new QueryView($valueObject);

        return $queryView;
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
        return $valueObject instanceof Query;
    }
}
