<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\Serializer\Values\ValueInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final class SerializerListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::VIEW => 'onView'];
    }

    /**
     * Serializes the value provided by the event.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $value = $event->getControllerResult();
        if (!$value instanceof ValueInterface) {
            return;
        }

        $context = [];
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
