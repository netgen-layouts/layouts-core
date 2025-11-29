<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

use function sprintf;

final class ThrowableSerializerListener implements EventSubscriberInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private LoggerInterface $logger = new NullLogger(),
    ) {}

    public static function getSubscribedEvents(): array
    {
        // Must happen BEFORE Symfony Security component ExceptionListener
        return [ExceptionEvent::class => ['onException', 5]];
    }

    /**
     * Serializes the throwable if SetIsApiRequestListener::API_FLAG_NAME
     * is set to true.
     */
    public function onException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        $throwable = $event->getThrowable();
        if (!$throwable instanceof HttpExceptionInterface || $throwable->getStatusCode() >= 500) {
            $this->logger->critical(
                sprintf(
                    'Uncaught PHP Exception %s: "%s" at %s line %s',
                    $throwable::class,
                    $throwable->getMessage(),
                    $throwable->getFile(),
                    $throwable->getLine(),
                ),
                ['error' => $throwable],
            );
        }

        $response = new JsonResponse();
        $response->setContent($this->serializer->serialize($throwable, 'json'));

        $event->setResponse($response);
    }
}
