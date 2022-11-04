<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Context\Context;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function http_build_query;
use function mb_substr;
use function parse_str;
use function str_contains;
use function urlencode;

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
        $context = $this->context->all();

        $uri = $this->urlGenerator->generate(
            'nglayouts_ajax_block',
            [
                'blockId' => $block->getId()->toString(),
                'locale' => $block->getLocale(),
                'collectionIdentifier' => $collectionIdentifier,
                'nglContext' => $context,
            ],
        );

        $signedContext = $this->uriSigner->sign(
            '?' . http_build_query(['nglContext' => $context]),
        );

        parse_str(mb_substr($signedContext, 1), $params);

        $uri .= (!str_contains($uri, '?') ? '?' : '&') . '_hash=' . urlencode($params['_hash']);

        if ($page > 1) {
            $uri .= '&page=' . $page;
        }

        return $uri;
    }
}
