<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;

/**
 * @final
 */
class TwigExtensionsListener implements EventSubscriberInterface
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var string[]
     */
    private $extensions;

    public function __construct(Environment $twig, array $extensions)
    {
        $this->twig = $twig;
        $this->extensions = $extensions;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 1024]];
    }

    /**
     * Adds the Twig extensions to the environment if they don't already exist.
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        foreach ($this->extensions as $extension) {
            if (!class_exists($extension) || !is_a($extension, ExtensionInterface::class, true)) {
                continue;
            }

            if ($this->twig->hasExtension($extension)) {
                continue;
            }

            $this->twig->addExtension(new $extension());
        }
    }
}
