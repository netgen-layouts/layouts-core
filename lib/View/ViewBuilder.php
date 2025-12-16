<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Netgen\Layouts\Event\BuildViewEvent;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\View\Provider\ViewProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @param iterable<\Netgen\Layouts\View\Provider\ViewProviderInterface<object>> $viewProviders
     */
    public function __construct(
        private TemplateResolverInterface $templateResolver,
        private EventDispatcherInterface $eventDispatcher,
        private iterable $viewProviders,
    ) {}

    public function buildView(object $value, string $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = []): ViewInterface
    {
        $viewProvider = $this->getViewProvider($value);

        $view = $viewProvider->provideView($value, $parameters);
        $view->context = $context;
        $view->addParameters($parameters);
        $view->addParameter('view_context', $context);

        $this->templateResolver->resolveTemplate($view);

        $event = new BuildViewEvent($view);
        $this->eventDispatcher->dispatch($event);

        $event = new BuildViewEvent($view);
        $this->eventDispatcher->dispatch($event, BuildViewEvent::getEventName($view->identifier));

        return $view;
    }

    /**
     * Returns the view provider that supports the given value.
     *
     * @return \Netgen\Layouts\View\Provider\ViewProviderInterface<object>
     */
    private function getViewProvider(object $value): ViewProviderInterface
    {
        foreach ($this->viewProviders as $viewProvider) {
            if ($viewProvider->supports($value)) {
                return $viewProvider;
            }
        }

        throw ViewProviderException::noViewProvider($value::class);
    }
}
