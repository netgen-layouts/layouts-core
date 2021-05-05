<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Context\Context;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function str_contains;

final class RouteGenerator
{
    private Context $context;

    private UriSigner $uriSigner;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        Context $context,
        UriSigner $uriSigner,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->context = $context;
        $this->uriSigner = $uriSigner;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Generates and signs the URI for provided block, collection and page number.
     */
    public function __invoke(Block $block, string $collectionIdentifier, int $page): string
    {
        $routeParams = [
            'blockId' => $block->getId()->toString(),
            'locale' => $block->getLocale(),
            'collectionIdentifier' => $collectionIdentifier,
            'nglContext' => $this->context->all(),
        ];

        $signedUri = $this->uriSigner->sign(
            $this->urlGenerator->generate('nglayouts_ajax_block', $routeParams),
        );

        if ($page > 1) {
            $signedUri .= (!str_contains($signedUri, '?') ? '?' : '&') . 'page=' . $page;
        }

        return $signedUri;
    }
}
