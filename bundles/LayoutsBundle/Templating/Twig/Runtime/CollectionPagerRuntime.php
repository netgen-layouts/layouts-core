<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Exception\InvalidArgumentException;
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

    public function __construct(callable $routeGenerator, ViewInterface $pagerfantaView)
    {
        $this->routeGenerator = $routeGenerator;
        $this->pagerfantaView = $pagerfantaView;
    }

    /**
     * Renders the provided Pagerfanta view.
     */
    public function renderCollectionPager(Pagerfanta $pagerfanta, Block $block, string $collectionIdentifier, array $options = []): string
    {
        $options['block'] = $block;
        $options['collection_identifier'] = $collectionIdentifier;

        return $this->pagerfantaView->render($pagerfanta, $this->routeGenerator, $options);
    }

    /**
     * Returns the URL of the provided pager and page number.
     */
    public function getCollectionPageUrl(Pagerfanta $pagerfanta, Block $block, string $collectionIdentifier, int $page = 1): string
    {
        if ($page < 1 || $page > $pagerfanta->getNbPages()) {
            throw new InvalidArgumentException(
                'page',
                sprintf('Page %d is out of bounds', $page)
            );
        }

        return call_user_func($this->routeGenerator, $block, $collectionIdentifier, $page);
    }
}
