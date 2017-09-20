<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionSerializerListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(SerializerInterface $serializer, LoggerInterface $logger = null)
    {
        $this->serializer = $serializer;
        $this->logger = $logger ?: new NullLogger();
    }

    public static function getSubscribedEvents()
    {
        // Must happen BEFORE Symfony Security component ExceptionListener
        return array(KernelEvents::EXCEPTION => array('onException', 5));
    }

    /**
     * Serializes the exception if {@link SetIsApiRequestListener::API_FLAG_NAME}
     * is set to true.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
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
                array('exception' => $exception)
            );
        }

        $response = new JsonResponse();
        $response->setContent($this->serializer->serialize($exception, 'json'));

        $event->setResponse($response);
    }
}
