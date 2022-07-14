<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Netgen\Layouts\Utils\BackwardsCompatibility\ExceptionEventThrowableTrait;
use Netgen\Layouts\Utils\BackwardsCompatibility\MainRequestEventTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

use function get_debug_type;
use function sprintf;

final class ExceptionSerializerListener implements EventSubscriberInterface
{
    use ExceptionEventThrowableTrait;
    use MainRequestEventTrait;

    private SerializerInterface $serializer;

    private LoggerInterface $logger;

    public function __construct(SerializerInterface $serializer, ?LoggerInterface $logger = null)
    {
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        // Must happen BEFORE Symfony Security component ExceptionListener
        return [KernelEvents::EXCEPTION => ['onException', 5]];
    }

    /**
     * Serializes the exception if SetIsApiRequestListener::API_FLAG_NAME
     * is set to true.
     *
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    public function onException($event): void
    {
        if (!$this->isMainRequest($event)) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        $exception = $this->getThrowable($event);
        if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
            $this->logger->critical(
                sprintf(
                    'Uncaught PHP Exception %s: "%s" at %s line %s',
                    get_debug_type($exception),
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine(),
                ),
                ['exception' => $exception],
            );
        }

        $response = new JsonResponse();
        $response->setContent($this->serializer->serialize($exception, 'json'));

        $event->setResponse($response);
    }
}
