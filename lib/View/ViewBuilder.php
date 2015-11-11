<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Registry\ViewTemplateProviderRegistry;
use Netgen\BlockManager\API\Values\Value;
use InvalidArgumentException;

class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Registry\ViewTemplateProviderRegistry
     */
    protected $viewTemplateProviderRegistry;

    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProvider[]
     */
    protected $viewProviders = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Registry\ViewTemplateProviderRegistry $viewTemplateProviderRegistry
     * @param \Netgen\BlockManager\View\Provider\ViewProvider[] $viewProviders
     */
    public function __construct(ViewTemplateProviderRegistry $viewTemplateProviderRegistry, array $viewProviders = array())
    {
        $this->viewTemplateProviderRegistry = $viewTemplateProviderRegistry;
        $this->viewProviders = $viewProviders;
    }

    /**
     * Builds the view.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param array $parameters
     * @param string $context
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView(Value $value, array $parameters = array(), $context = 'view')
    {
        foreach ($this->viewProviders as $viewProvider) {
            if (!$viewProvider->supports($value)) {
                continue;
            }

            $view = $viewProvider->provideView($value, $parameters, $context);
        }

        if (!isset($view)) {
            throw new InvalidArgumentException(
                sprintf(
                    'No view providers found for %s class.',
                    get_class($value)
                )
            );
        }

        $viewTemplateProvider = $this->viewTemplateProviderRegistry->getViewTemplateProvider($view);
        $view->setTemplate(
            $viewTemplateProvider->provideTemplate($view)
        );

        return $view;
    }
}
