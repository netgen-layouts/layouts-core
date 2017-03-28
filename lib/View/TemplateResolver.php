<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Exception\View\TemplateResolverException;
use Netgen\BlockManager\View\Matcher\MatcherInterface;

class TemplateResolver implements TemplateResolverInterface
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface[]
     */
    protected $matchers = array();

    /**
     * @var array
     */
    protected $viewConfig = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Matcher\MatcherInterface[] $matchers
     * @param array $viewConfig
     */
    public function __construct(array $matchers = array(), array $viewConfig = array())
    {
        foreach ($matchers as $matcher) {
            if (!$matcher instanceof MatcherInterface) {
                throw TemplateResolverException::invalidTemplateMatcher(get_class($matcher));
            }
        }

        $this->matchers = $matchers;
        $this->viewConfig = $viewConfig;
    }

    /**
     * Resolves a view template.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @throws \RuntimeException If there's no template defined for specified view
     */
    public function resolveTemplate(ViewInterface $view)
    {
        $viewContext = $view->getContext();
        $fallbackViewContext = $view->getFallbackContext();

        $contextList = array($viewContext);
        if (is_string($fallbackViewContext)) {
            $contextList[] = $fallbackViewContext;
        }

        $viewIdentifier = $view->getIdentifier();
        foreach ($contextList as $context) {
            if (!isset($this->viewConfig[$viewIdentifier][$context])) {
                continue;
            }

            foreach ($this->viewConfig[$viewIdentifier][$context] as $config) {
                if (!$this->matches($view, $config['match'])) {
                    continue;
                }

                $view->setTemplate($config['template']);
                $view->addParameters($config['parameters']);

                return;
            }
        }

        throw TemplateResolverException::noTemplateMatch($view->getIdentifier(), $viewContext);
    }

    /**
     * Matches the view to provided config with configured matchers.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param array $matchConfig
     *
     * @return bool
     */
    protected function matches(ViewInterface $view, array $matchConfig)
    {
        foreach ($matchConfig as $matcher => $matcherConfig) {
            if (!isset($this->matchers[$matcher])) {
                throw TemplateResolverException::noTemplateMatcher($matcher);
            }

            $matcherConfig = !is_array($matcherConfig) ? array($matcherConfig) : $matcherConfig;
            if (!$this->matchers[$matcher]->match($view, $matcherConfig)) {
                return false;
            }
        }

        return true;
    }
}
