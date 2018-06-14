<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use EdiModric\Twig\VersionExtension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use Twig\Extensions\IntlExtension;

/**
 * @final
 */
class TwigExtensionsListener implements EventSubscriberInterface
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    /**
     * Adds the Twig extensions to the environment if they don't already exist.
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$this->twig->hasExtension(IntlExtension::class)) {
            $this->twig->addExtension(new IntlExtension());
        }

        if (!$this->twig->hasExtension(VersionExtension::class)) {
            $this->twig->addExtension(new VersionExtension());
        }
    }
}
