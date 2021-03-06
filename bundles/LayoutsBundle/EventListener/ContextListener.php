<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener;

use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Context\ContextBuilderInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\MainRequestEventTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\UriSigner;
use function is_array;

final class ContextListener implements EventSubscriberInterface
{
    use MainRequestEventTrait;

    private Context $context;

    private ContextBuilderInterface $contextBuilder;

    private UriSigner $uriSigner;

    public function __construct(
        Context $context,
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
     *
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     */
    public function onKernelRequest($event): void
    {
        if (!$this->isMainRequest($event)) {
            return;
        }

        $request = $event->getRequest();
        if ($request->attributes->has('nglContext')) {
            $context = $request->attributes->get('nglContext');
            $context = is_array($context) ? $context : [];

            $this->context->add($context);

            return;
        }

        if ($request->query->has('nglContext')) {
            $this->context->add($this->getUriContext($request));

            return;
        }

        $this->contextBuilder->buildContext($this->context);
    }

    /**
     * Validates and returns the array with context information filled from the URI.
     *
     * @return array<string, mixed>
     */
    private function getUriContext(Request $request): array
    {
        $context = Kernel::VERSION_ID >= 50100 ?
            $request->query->all('nglContext') :
            $request->query->get('nglContext');

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
        if ($request->attributes->has('nglContextUri')) {
            return $request->attributes->get('nglContextUri');
        }

        return $request->getRequestUri();
    }
}
