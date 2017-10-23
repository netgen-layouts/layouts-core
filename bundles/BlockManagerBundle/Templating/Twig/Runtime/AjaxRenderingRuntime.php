<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Context\ContextInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\ViewFactoryInterface;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\RouterInterface;

final class AjaxRenderingRuntime
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
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Pagerfanta\View\ViewFactoryInterface
     */
    private $pagerfantaViewFactory;

    public function __construct(
        ContextInterface $context,
        UriSigner $uriSigner,
        RouterInterface $router,
        ViewFactoryInterface $pagerfantaViewFactory
    ) {
        $this->context = $context;
        $this->uriSigner = $uriSigner;
        $this->router = $router;
        $this->pagerfantaViewFactory = $pagerfantaViewFactory;
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
    public function renderAjaxBlockPager(Pagerfanta $pagerfanta, Block $block, $collectionIdentifier, $viewName, array $options = array())
    {
        $routeGenerator = $this->createRouteGenerator($block, $collectionIdentifier);

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
    public function getAjaxBlockPageUrl(Pagerfanta $pagerfanta, Block $block, $collectionIdentifier, $page = 1)
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
                $this->router->generate('ngbm_ajax_block', $routeParams)
            );

            if ($page > 1) {
                $signedUri .= '&' . 'page=' . (int) $page;
            }

            return $signedUri;
        };
    }
}
