<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use RuntimeException;

class TemplateResolver implements TemplateResolverInterface
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface[]
     */
    protected $matchers = array();

    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Matcher\MatcherInterface[] $matchers
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(array $matchers = array(), ConfigurationInterface $configuration)
    {
        $this->matchers = $matchers;
        $this->configuration = $configuration;
    }

    /**
     * Resolves a view template.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @throws \RuntimeException If there's no template defined for specified view
     *
     * @return string
     */
    public function resolveTemplate(ViewInterface $view)
    {
        $matchedConfig = false;
        $context = $view->getContext();
        $viewConfig = $this->configuration->getParameter($view->getAlias());

        if (!isset($viewConfig[$context])) {
            throw new RuntimeException(
                sprintf(
                    'No configuration could be found for context "%s" and view object "%s".',
                    $context,
                    get_class($view)
                )
            );
        }

        foreach ($viewConfig[$context] as $config) {
            $matchConfig = $config['match'];
            if (!$this->matches($view, $matchConfig)) {
                continue;
            }

            $matchedConfig = $config;
            break;
        }

        if (!is_array($matchedConfig) || !isset($matchedConfig['template'])) {
            throw new RuntimeException(
                sprintf(
                    'No templates could be found for view object "%s".',
                    get_class($view)
                )
            );
        }

        return $matchedConfig['template'];
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
                throw new RuntimeException(
                    sprintf(
                        'No matcher could be found with identifier "%s".',
                        $matcher
                    )
                );
            }

            if (!$this->matchers[$matcher] instanceof MatcherInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Matcher "%s" needs to implement MatcherInterface.',
                        $matcher
                    )
                );
            }

            $matcherConfig = !is_array($matcherConfig) ? array($matcherConfig) : $matcherConfig;
            $this->matchers[$matcher]->setConfig($matcherConfig);
            if (!$this->matchers[$matcher]->match($view)) {
                return false;
            }
        }

        return true;
    }
}
