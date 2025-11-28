<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\View\Provider\ViewProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function get_debug_type;
use function sprintf;

final class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @var \Netgen\Layouts\View\Provider\ViewProviderInterface[]
     */
    private array $viewProviders = [];

    /**
     * @param iterable<\Netgen\Layouts\View\Provider\ViewProviderInterface> $viewProviders
     */
    public function __construct(
        private TemplateResolverInterface $templateResolver,
        private EventDispatcherInterface $eventDispatcher,
        iterable $viewProviders,
    ) {
        foreach ($viewProviders as $viewProvider) {
            if ($viewProvider instanceof ViewProviderInterface) {
                $this->viewProviders[] = $viewProvider;
            }
        }
    }

    public function buildView(mixed $value, string $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = []): ViewInterface
    {
        $viewProvider = $this->getViewProvider($value);

        $view = $viewProvider->provideView($value, $parameters);
        $view->setContext($context);
        $view->addParameters($parameters);
        $view->addParameter('view_context', $context);

        $this->templateResolver->resolveTemplate($view);

        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch($event);
        $view->addParameters($event->parameters);

        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch($event, sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, $view::getIdentifier()));
        $view->addParameters($event->parameters);

        return $view;
    }

    /**
     * Returns the view provider that supports the given value.
     */
    private function getViewProvider(mixed $value): ViewProviderInterface
    {
        foreach ($this->viewProviders as $viewProvider) {
            if ($viewProvider->supports($value)) {
                return $viewProvider;
            }
        }

        throw ViewProviderException::noViewProvider(get_debug_type($value));
    }
}
