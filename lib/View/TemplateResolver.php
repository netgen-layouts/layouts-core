<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use RuntimeException;

class TemplateResolver implements TemplateResolverInterface
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface[]
     */
    protected $matchers = array();

    /**
     * @var array
     */
    protected $configurations = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Matcher\MatcherInterface[] $matchers
     * @param array $configurations
     */
    public function __construct(array $matchers = array(), array $configurations = array())
    {
        $this->matchers = $matchers;
        $this->configurations = $configurations;
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

        if (!isset($this->configurations[$view->getAlias()][$context])) {
            throw new RuntimeException(
                sprintf(
                    'No template could be found for context "%s" and view object "%s".',
                    $context,
                    get_class($view)
                )
            );
        }

        foreach ($this->configurations[$view->getAlias()][$context] as $config) {
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
                    'No template could be found for context "%s" view object "%s".',
                    $context,
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
                        'No template matcher could be found with identifier "%s".',
                        $matcher
                    )
                );
            }

            if (!$this->matchers[$matcher] instanceof MatcherInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Template matcher "%s" needs to implement MatcherInterface.',
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
