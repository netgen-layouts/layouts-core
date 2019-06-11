<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

final class RequestBodyListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Serializer\Encoder\DecoderInterface
     */
    private $decoder;

    public function __construct(DecoderInterface $decoder)
    {
        $this->decoder = $decoder;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    /**
     * Decodes the request data into request parameter bag.
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }

        if ($request->attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        if (!$this->isDecodeable($request)) {
            return;
        }

        try {
            $data = $this->decoder->decode((string) $request->getContent(), 'json');
        } catch (UnexpectedValueException $e) {
            throw new BadRequestHttpException('Request body has an invalid format', $e);
        }

        if (!is_array($data)) {
            throw new BadRequestHttpException('Request body has an invalid format');
        }

        $request->attributes->set('data', new ParameterBag($data));
    }

    /**
     * Check if we should try to decode the body.
     */
    private function isDecodeable(Request $request): bool
    {
        if (
            !in_array(
                $request->getMethod(),
                [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH, Request::METHOD_DELETE],
                true
            )
        ) {
            return false;
        }

        return $request->getContentType() === 'json';
    }
}
