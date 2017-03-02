<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Michelf\MarkdownInterface;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

class MarkdownHandler extends BlockDefinitionHandler
{
    /**
     * @var \Michelf\MarkdownInterface
     */
    protected $markdownParser;

    /**
     * Constructor.
     *
     * @param \Michelf\MarkdownInterface $markdownParser
     */
    public function __construct(MarkdownInterface $markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'content',
            ParameterType\TextType::class
        );

        $this->buildCommonParameters($builder);
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getDynamicParameters(Block $block, array $parameters = array())
    {
        $rawContent = $block->getParameter('content')->getValue();

        return array(
            'html' => function () use ($rawContent) {
                return $this->markdownParser->transform($rawContent);
            },
        );
    }
}
