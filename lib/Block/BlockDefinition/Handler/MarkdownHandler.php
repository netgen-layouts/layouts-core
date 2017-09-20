<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Michelf\MarkdownInterface;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

class MarkdownHandler extends BlockDefinitionHandler
{
    /**
     * @var \Michelf\MarkdownInterface
     */
    private $markdownParser;

    public function __construct(MarkdownInterface $markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'content',
            ParameterType\TextType::class
        );
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
        $rawContent = $block->getParameter('content')->getValue();

        $params['html'] = function () use ($rawContent) {
            return $this->markdownParser->transform($rawContent);
        };
    }
}
