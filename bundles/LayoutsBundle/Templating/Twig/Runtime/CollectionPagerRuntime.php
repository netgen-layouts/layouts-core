<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime;

use Closure;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Exception\InvalidArgumentException;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;

use function call_user_func;
use function sprintf;

final class CollectionPagerRuntime
{
    private Closure $routeGenerator;

    private ViewInterface $pagerfantaView;

    public function __construct(callable $routeGenerator, ViewInterface $pagerfantaView)
    {
        $this->routeGenerator = Closure::fromCallable($routeGenerator);
        $this->pagerfantaView = $pagerfantaView;
    }

    /**
     * Renders the provided Pagerfanta view.
     *
     * @param PagerfantaInterface<\Netgen\Layouts\Collection\Result\Result> $pagerfanta
     * @param array<string, mixed> $options
     */
    public function renderCollectionPager(PagerfantaInterface $pagerfanta, Block $block, string $collectionIdentifier, array $options = []): string
    {
        $options['block'] = $block;
        $options['collection_identifier'] = $collectionIdentifier;

        return $this->pagerfantaView->render($pagerfanta, $this->routeGenerator, $options);
    }

    /**
     * Returns the URL of the provided pager and page number.
     *
     * @param PagerfantaInterface<\Netgen\Layouts\Collection\Result\Result> $pagerfanta
     */
    public function getCollectionPageUrl(PagerfantaInterface $pagerfanta, Block $block, string $collectionIdentifier, int $page = 1): string
    {
        if ($page < 1 || $page > $pagerfanta->getNbPages()) {
            throw new InvalidArgumentException(
                'page',
                sprintf('Page %d is out of bounds', $page),
            );
        }

        return call_user_func($this->routeGenerator, $block, $collectionIdentifier, $page);
    }
}
