<?php

namespace Netgen\BlockManager\Layout\Resolver\RuleLoader;

use Netgen\BlockManager\Layout\Resolver\RuleHandler\RuleHandlerInterface;
use Netgen\BlockManager\Layout\Resolver\RuleBuilder\RuleBuilderInterface;
use Netgen\BlockManager\Layout\Resolver\Target;

class RuleLoader implements RuleLoaderInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\RuleHandler\RuleHandlerInterface
     */
    protected $ruleHandler;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\RuleBuilder\RuleBuilderInterface
     */
    protected $ruleBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\RuleHandler\RuleHandlerInterface $ruleHandler
     * @param \Netgen\BlockManager\Layout\Resolver\RuleBuilder\RuleBuilderInterface $ruleBuilder
     */
    public function __construct(RuleHandlerInterface $ruleHandler, RuleBuilderInterface $ruleBuilder)
    {
        $this->ruleHandler = $ruleHandler;
        $this->ruleBuilder = $ruleBuilder;
    }

    /**
     * Loads the rules based on target.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Target $target
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule[]
     */
    public function loadRules(Target $target)
    {
        $targetValues = $target->getValues();
        if (empty($targetValues)) {
            return array();
        }

        $data = $this->ruleHandler->loadRules(
            $target->getIdentifier(),
            $targetValues
        );

        if (empty($data)) {
            return array();
        }

        return $this->ruleBuilder->buildRules($target, $data);
    }
}
