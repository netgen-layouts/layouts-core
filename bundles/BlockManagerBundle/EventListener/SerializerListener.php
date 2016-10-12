<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\Serializer\Values\ValueInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::VIEW => 'onView');
    }

    /**
     * Serializes the value.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        $value = $event->getControllerResult();
        if (!$value instanceof ValueInterface) {
            return;
        }

        $context = array();
        if ($request->query->get('html') === 'false') {
            $context['disable_html'] = true;
        }

        $response = new JsonResponse(null, $value->getStatusCode());
        $response->setContent(
            $this->serializer->serialize($value, 'json', $context)
        );

        $event->setResponse($response);
    }
}
