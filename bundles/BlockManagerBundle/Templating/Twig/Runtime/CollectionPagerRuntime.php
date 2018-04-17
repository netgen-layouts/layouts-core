<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\ViewInterface;

final class CollectionPagerRuntime
{
    /**
     * @var callable
     */
    private $routeGenerator;

    /**
     * @var \Pagerfanta\View\ViewInterface
     */
    private $pagerfantaView;

    /**
     * @param callable $routeGenerator
     * @param \Pagerfanta\View\ViewInterface $pagerfantaView
     */
    public function __construct(callable $routeGenerator, ViewInterface $pagerfantaView)
    {
        $this->routeGenerator = $routeGenerator;
        $this->pagerfantaView = $pagerfantaView;
    }

    /**
     * Renders the provided Pagerfanta view.
     *
     * @param \Pagerfanta\Pagerfanta $pagerfanta
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param array $options
     *
     * @return string
     */
    public function renderCollectionPager(Pagerfanta $pagerfanta, Block $block, $collectionIdentifier, array $options = [])
    {
        $options['block'] = $block;
        $options['collection_identifier'] = $collectionIdentifier;

        return $this->pagerfantaView->render($pagerfanta, $this->routeGenerator, $options);
    }

    /**
     * Returns the URL of the provided pager and page number.
     *
     * @param \Pagerfanta\Pagerfanta $pagerfanta
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param int $page
     *
     * @return string
     */
    public function getCollectionPageUrl(Pagerfanta $pagerfanta, Block $block, $collectionIdentifier, $page = 1)
    {
        if ($page < 1 || $page > $pagerfanta->getNbPages()) {
            throw new InvalidArgumentException(
                'page',
                sprintf('Page %d is out of bounds', (int) $page)
            );
        }

        $routeGenerator = $this->routeGenerator;

        return $routeGenerator($block, $collectionIdentifier, $page);
    }
}
