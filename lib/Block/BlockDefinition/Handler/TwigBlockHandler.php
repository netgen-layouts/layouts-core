<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\BlockDefinition\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

class TwigBlockHandler extends BlockDefinitionHandler
{
    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'block_name',
            ParameterType\IdentifierType::class
        );

        $this->buildCommonParameters($builder);
    }

    /**
     * Returns the name of the Twig block to use.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return string
     */
    public function getTwigBlockName(Block $block)
    {
        return $block->getParameter('block_name')->getValue();
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getDynamicParameters(Block $block, array $parameters = array())
    {
        return array(
            'content' => function () use ($block, $parameters) {
                if (!isset($parameters['twigTemplate'])) {
                    return '';
                }

                if (!$parameters['twigTemplate'] instanceof ContextualizedTwigTemplate) {
                    return '';
                }

                return $parameters['twigTemplate']->renderBlock(
                    $this->getTwigBlockName($block)
                );
            },
        );
    }
}
