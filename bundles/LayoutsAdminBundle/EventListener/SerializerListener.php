<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\AbstractValue;
use Netgen\Layouts\Utils\BackwardsCompatibility\MainRequestEventTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final class SerializerListener implements EventSubscriberInterface
{
    use MainRequestEventTrait;

    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => 'onView'];
    }

    /**
     * Serializes the value provided by the event.
     *
     * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
     */
    public function onView($event): void
    {
        if (!$this->isMainRequest($event)) {
            return;
        }

        $request = $event->getRequest();
        $value = $event->getControllerResult();
        if (!$value instanceof AbstractValue) {
            return;
        }

        $context = [];
        if ($request->query->get('html') === 'false') {
            $context['disable_html'] = true;
        }

        $response = new JsonResponse(null, $value->getStatusCode());
        $response->setContent(
            $this->serializer->serialize($value, 'json', $context),
        );

        $event->setResponse($response);
    }
}
