<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class RequestBodyListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Serializer\Encoder\DecoderInterface
     */
    protected $decoder;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Serializer\Encoder\DecoderInterface $decoder
     */
    public function __construct(DecoderInterface $decoder)
    {
        $this->decoder = $decoder;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
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

        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
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
    protected function isDecodeable(Request $request)
    {
        if (!in_array($request->getMethod(), array('POST', 'PUT', 'PATCH', 'DELETE'))) {
            return false;
        }

        return $request->getContentType() === 'json';
    }
}
