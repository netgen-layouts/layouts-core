<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Value;
use InvalidArgumentException;

class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProvider[]
     */
    protected $viewProviders = array();

    /**
     * @var \Netgen\BlockManager\View\TemplateResolver\ViewTemplateResolver[]
     */
    protected $viewTemplateResolvers = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Provider\ViewProvider[] $viewProviders
     * @param \Netgen\BlockManager\View\TemplateResolver\ViewTemplateResolver[] $viewTemplateResolvers
     */
    public function __construct(array $viewProviders = array(), array $viewTemplateResolvers = array())
    {
        $this->viewProviders = $viewProviders;
        $this->viewTemplateResolvers = $viewTemplateResolvers;
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

        foreach ($this->viewTemplateResolvers as $viewTemplateResolver) {
            if (!$viewTemplateResolver->supports($view)) {
                continue;
            }

            $view->setTemplate(
                $viewTemplateResolver->resolveTemplate($view)
            );
        }

        return $view;
    }
}
