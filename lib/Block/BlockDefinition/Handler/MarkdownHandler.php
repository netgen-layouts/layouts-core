<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Parameters\Parameter\Text;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Michelf\MarkdownInterface;

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
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'content' => new Text(),
        ) + $this->getCommonParameters();
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return array
     */
    public function getDynamicParameters(Block $block)
    {
        $rawContent = $block->getParameter('content')->getValue();

        return array(
            'html' => function () use ($rawContent) {
                return $this->markdownParser->transform($rawContent);
            },
        );
    }
}
