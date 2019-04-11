<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener;

use Netgen\BlockManager\Context\ContextBuilderInterface;
use Netgen\BlockManager\Context\ContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\UriSigner;

final class ContextListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Context\ContextInterface
     */
    private $context;

    /**
     * @var \Netgen\BlockManager\Context\ContextBuilderInterface
     */
    private $contextBuilder;

    /**
     * @var \Symfony\Component\HttpKernel\UriSigner
     */
    private $uriSigner;

    public function __construct(
        ContextInterface $context,
        ContextBuilderInterface $contextBuilder,
        UriSigner $uriSigner
    ) {
        $this->context = $context;
        $this->contextBuilder = $contextBuilder;
        $this->uriSigner = $uriSigner;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    /**
     * Builds the context object.
     *
     * If the context is available in query parameters and the URI signature is valid,
     * it will be used, otherwise, provided builder will be used.
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->attributes->has('ngbmContext')) {
            $context = $request->attributes->get('ngbmContext');
            $context = is_array($context) ? $context : [];

            $this->context->add($context);

            return;
        }

        if ($request->query->has('ngbmContext')) {
            $this->context->add($this->getUriContext($request));

            return;
        }

        $this->contextBuilder->buildContext($this->context);
    }

    /**
     * Validates and returns the array with context information filled from the URI.
     */
    private function getUriContext(Request $request): array
    {
        $context = $request->query->get('ngbmContext');
        $context = is_array($context) ? $context : [];

        if (!$this->uriSigner->check($this->getUri($request))) {
            return [];
        }

        return $context;
    }

    /**
     * Returns the URI with the context from the request. This allows
     * overriding the URI with a value stored in request attributes if,
     * for example, there's need to pre-process the URI before checking
     * the signature.
     */
    private function getUri(Request $request): string
    {
        if ($request->attributes->has('ngbmContextUri')) {
            return $request->attributes->get('ngbmContextUri');
        }

        return $request->getRequestUri();
    }
}
