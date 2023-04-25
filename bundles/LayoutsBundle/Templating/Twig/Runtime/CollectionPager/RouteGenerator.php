<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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

    private RequestStack $requestStack;

    public function __construct(
        Context $context,
        UriSigner $uriSigner,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    ) {
        $this->context = $context;
        $this->uriSigner = $uriSigner;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * Generates and signs the URI for provided block, collection and page number.
     */
    public function __invoke(Block $block, string $collectionIdentifier, int $page): string
    {
        $context = $this->context->all();
        $currentRequest = $this->requestStack->getCurrentRequest();

        $routeParams = [
            'blockId' => $block->getId()->toString(),
            'locale' => $block->getLocale(),
            'collectionIdentifier' => $collectionIdentifier,
            'nglContext' => $context,
        ];

        if ($currentRequest instanceof Request) {
            $routeParams += $currentRequest->query->all();
        }

        $uri = $this->urlGenerator->generate(
            $block->isPublished() ? 'nglayouts_ajax_block' : 'nglayouts_ajax_block_draft',
            $routeParams,
        );

        $signedContext = $this->uriSigner->sign(
            '?' . http_build_query(['nglContext' => $context]),
        );

        parse_str(mb_substr($signedContext, 1), $params);

        /** @var string $signature */
        $signature = $params['_hash'];

        $uri .= (!str_contains($uri, '?') ? '?' : '&') . '_hash=' . urlencode($signature);

        if ($page > 1) {
            $uri .= '&page=' . $page;
        }

        return $uri;
    }
}
