<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\Layouts\Collection\Result\ResultSet;

final class LoadCollectionResult extends AbstractController
{
    private PagerFactory $pagerFactory;

    public function __construct(PagerFactory $pagerFactory)
    {
        $this->pagerFactory = $pagerFactory;
    }

    /**
     * Returns the collection result.
     */
    public function __invoke(Block $block, string $collectionIdentifier): Value
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $collection = $block->getCollection($collectionIdentifier);

        // In non AJAX scenarios, we're always rendering the first page of the collection
        // as specified by offset and limit in the collection itself
        $pager = $this->pagerFactory->getPager($collection, 1, null, ResultSet::INCLUDE_ALL_ITEMS);

        return new Value($pager->getCurrentPageResults());
    }
}
