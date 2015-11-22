<?php

namespace Netgen\BlockManager\LayoutResolver\RuleLoader;

use Netgen\BlockManager\LayoutResolver\RuleHandler\RuleHandlerInterface;
use Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilderInterface;
use Netgen\BlockManager\LayoutResolver\Target;

class RuleLoader implements RuleLoaderInterface
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\RuleHandler\RuleHandlerInterface
     */
    protected $ruleHandler;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilderInterface
     */
    protected $ruleBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\LayoutResolver\RuleHandler\RuleHandlerInterface $ruleHandler
     * @param \Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilderInterface $ruleBuilder
     */
    public function __construct(RuleHandlerInterface $ruleHandler, RuleBuilderInterface $ruleBuilder)
    {
        $this->ruleHandler = $ruleHandler;
        $this->ruleBuilder = $ruleBuilder;
    }

    /**
     * Loads the rules based on target.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Target $target
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule[]
     */
    public function loadRules(Target $target)
    {
        if (empty($target->identifier) || empty($target->values)) {
            return array();
        }

        $data = $this->ruleHandler->loadRules(
            $target->identifier,
            $target->values
        );

        if (empty($data)) {
            return array();
        }

        return $this->ruleBuilder->buildRules($target, $data);
    }
}
