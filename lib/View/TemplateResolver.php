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
                throw new RuntimeException(
                    sprintf(
                        'Template matcher "%s" needs to implement MatcherInterface.',
                        get_class($matcher)
                    )
                );
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
     *
     * @return string
     */
    public function resolveTemplate(ViewInterface $view)
    {
        $context = $view->getContext();
        $viewIdentifier = $view->getIdentifier();

        if (!isset($this->viewConfig[$viewIdentifier][$context])) {
            throw new RuntimeException(
                sprintf(
                    'No view config could be found for view object "%s" and context "%s".',
                    get_class($view),
                    $context
                )
            );
        }

        foreach ($this->viewConfig[$viewIdentifier][$context] as $config) {
            if (!$this->matches($view, $config['match'])) {
                continue;
            }

            return $config['template'];
        }

        throw new RuntimeException(
            sprintf(
                'No template match could be found for view object "%s" and context "%s".',
                get_class($view),
                $context
            )
        );
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

            $matcherConfig = !is_array($matcherConfig) ? array($matcherConfig) : $matcherConfig;
            if (!$this->matchers[$matcher]->match($view, $matcherConfig)) {
                return false;
            }
        }

        return true;
    }
}
