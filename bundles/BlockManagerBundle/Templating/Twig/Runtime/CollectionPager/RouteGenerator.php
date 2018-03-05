<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPager;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Context\ContextInterface;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RouteGenerator
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

    public function __construct(
        ContextInterface $context,
        UriSigner $uriSigner,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->context = $context;
        $this->uriSigner = $uriSigner;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Generates and signs the URI for provided block, collection and page number.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param int $page
     *
     * @return string
     */
    public function __invoke(Block $block, $collectionIdentifier, $page)
    {
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
            $signedUri .= (mb_strpos($signedUri, '?') === false ? '?' : '&') . 'page=' . (int) $page;
        }

        return $signedUri;
    }
}
