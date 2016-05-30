<?php

namespace Netgen\BlockManager\View;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Netgen\BlockManager\API\Values\Value;
use RuntimeException;

class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface[]
     */
    protected $viewProviders = array();

    /**
     * @var \Netgen\BlockManager\View\TemplateResolverInterface
     */
    protected $templateResolver;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Provider\ViewProviderInterface[] $viewProviders
     * @param \Netgen\BlockManager\View\TemplateResolverInterface $templateResolver
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(array $viewProviders = array(), TemplateResolverInterface $templateResolver, EventDispatcherInterface $eventDispatcher)
    {
        $this->viewProviders = $viewProviders;
        $this->templateResolver = $templateResolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Builds the view.
     *
     * @param mixed $valueObject
     * @param string $context
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView($valueObject, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array())
    {
        foreach ($this->viewProviders as $viewProvider) {
            if (!$viewProvider instanceof ViewProviderInterface) {
                throw new RuntimeException(
                    sprintf(
                        'View provider for "%s" value object needs to implement ViewProviderInterface.',
                        get_class($valueObject)
                    )
                );
            }

            if (!$viewProvider->supports($valueObject)) {
                continue;
            }

            $view = $viewProvider->provideView($valueObject, $parameters);
            $view->setContext($context);
            $view->addParameters($parameters);
            $view->addParameters(array('view_context' => $context));

            $event = new CollectViewParametersEvent($view);
            $this->eventDispatcher->dispatch(ViewEvents::BUILD_VIEW, $event);
            $view->addParameters($event->getViewParameters());

            break;
        }

        if (!isset($view)) {
            throw new RuntimeException(
                sprintf(
                    'No view providers found for "%s" value object.',
                    get_class($valueObject)
                )
            );
        }

        $view->setTemplate(
            $this->templateResolver->resolveTemplate($view)
        );

        return $view;
    }
}
