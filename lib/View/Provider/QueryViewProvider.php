<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\View\QueryView;

class QueryViewProvider implements ViewProviderInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     */
    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Provides the view.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView(Value $value)
    {
        /** @var \Netgen\BlockManager\API\Values\Collection\Query $value */
        $queryView = new QueryView();

        $collection = $this->collectionService->loadCollection(
            $value->getCollectionId(),
            $value->getStatus()
        );

        $queryView->setQuery($value);
        $queryView->setParameters(array('collection' => $collection));

        return $queryView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return bool
     */
    public function supports(Value $value)
    {
        return $value instanceof Query;
    }
}
