<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

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

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => 'onKernelRequest');
    }

    /**
     * Decodes the request data into request parameter bag.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
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
            $data = $this->decoder->decode($request->getContent(), 'json');
        } catch (UnexpectedValueException $e) {
            throw new BadRequestHttpException('Request body has an invalid format', $e);
        }

        if (!is_array($data)) {
            throw new BadRequestHttpException('Request body has an invalid format');
        }

        $request->request = new ParameterBag($data);
    }

    /**
     * Check if we should try to decode the body.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    private function isDecodeable(Request $request)
    {
        if (
            !in_array(
                $request->getMethod(),
                array(Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH, Request::METHOD_DELETE),
                true
            )
        ) {
            return false;
        }

        return $request->getContentType() === 'json';
    }
}
