<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\ViewFactoryInterface;

final class CollectionPagerRuntime
{
    /**
     * @var callable
     */
    private $routeGenerator;

    /**
     * @var \Pagerfanta\View\ViewFactoryInterface
     */
    private $pagerfantaViewFactory;

    /**
     * @var string
     */
    private $defaultPagerfantaView;

    /**
     * @param callable $routeGenerator
     * @param \Pagerfanta\View\ViewFactoryInterface $pagerfantaViewFactory
     * @param string $defaultPagerfantaView
     */
    public function __construct(
        callable $routeGenerator,
        ViewFactoryInterface $pagerfantaViewFactory,
        $defaultPagerfantaView
    ) {
        $this->routeGenerator = $routeGenerator;
        $this->pagerfantaViewFactory = $pagerfantaViewFactory;
        $this->defaultPagerfantaView = $defaultPagerfantaView;
    }

    /**
     * Renders the provided Pagerfanta view.
     *
     * @param \Pagerfanta\Pagerfanta $pagerfanta
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param string $viewName
     * @param array $options
     *
     * @return string
     */
    public function renderCollectionPager(Pagerfanta $pagerfanta, Block $block, $collectionIdentifier, $viewName = null, array $options = array())
    {
        $viewName = $viewName !== null ? $viewName : $this->defaultPagerfantaView;

        $options['block'] = $block;
        $options['collection_identifier'] = $collectionIdentifier;

        return $this->pagerfantaViewFactory->get($viewName)->render($pagerfanta, $this->routeGenerator, $options);
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
