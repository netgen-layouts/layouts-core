<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Context\ContextInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\ViewFactoryInterface;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CollectionPagerRuntime
{
    /**
     * @var \Netgen\BlockManager\Context\ContextInterface
     */
    private $context;

    /**
     * @var \Symfony\Component\HttpKernel\UriSigner
     */
    private $uriSigner;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \Pagerfanta\View\ViewFactoryInterface
     */
    private $pagerfantaViewFactory;

    /**
     * @var string
     */
    private $defaultPagerfantaView;

    public function __construct(
        ContextInterface $context,
        UriSigner $uriSigner,
        UrlGeneratorInterface $urlGenerator,
        ViewFactoryInterface $pagerfantaViewFactory,
        $defaultPagerfantaView
    ) {
        $this->context = $context;
        $this->uriSigner = $uriSigner;
        $this->urlGenerator = $urlGenerator;
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
        $routeGenerator = $this->createRouteGenerator($block, $collectionIdentifier);
        $viewName = $viewName !== null ? $viewName : $this->defaultPagerfantaView;

        $options['block'] = $block;
        $options['collection_identifier'] = $collectionIdentifier;

        return $this->pagerfantaViewFactory->get($viewName)->render($pagerfanta, $routeGenerator, $options);
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

        $routeGenerator = $this->createRouteGenerator($block, $collectionIdentifier);

        return $routeGenerator($page);
    }

    /**
     * Creates a callable that will be used by Pagerfanta view factory to generate
     * the URLs for each page.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     *
     * @return \Closure
     */
    private function createRouteGenerator(Block $block, $collectionIdentifier)
    {
        return function ($page) use ($block, $collectionIdentifier) {
            $routeParams = array(
                'blockId' => $block->getId(),
                'locale' => $block->getLocale(),
                'collectionIdentifier' => $collectionIdentifier,
                'ngbmContext' => $this->context->all(),
            );

            $signedUri = $this->uriSigner->sign(
                $this->urlGenerator->generate('ngbm_ajax_block', $routeParams)
            );

            if ($page > 1) {
                $signedUri .= '&' . 'page=' . (int) $page;
            }

            return $signedUri;
        };
    }
}
