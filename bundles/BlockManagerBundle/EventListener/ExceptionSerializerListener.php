<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final class ExceptionSerializerListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(SerializerInterface $serializer, LoggerInterface $logger = null)
    {
        $this->serializer = $serializer;
        $this->logger = $logger ?: new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        // Must happen BEFORE Symfony Security component ExceptionListener
        return [KernelEvents::EXCEPTION => ['onException', 5]];
    }

    /**
     * Serializes the exception if SetIsApiRequestListener::API_FLAG_NAME
     * is set to true.
     */
    public function onException(GetResponseForExceptionEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        $exception = $event->getException();

        if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
            $this->logger->critical(
                sprintf(
                    'Uncaught PHP Exception %s: "%s" at %s line %s',
                    get_class($exception),
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                ),
                ['exception' => $exception]
            );
        }

        $response = new JsonResponse();
        $response->setContent($this->serializer->serialize($exception, 'json'));

        $event->setResponse($response);
    }
}
