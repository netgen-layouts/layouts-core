<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Twig_Node_Expression;
use Twig_Compiler;
use Twig_Node;

class RenderBlock extends Twig_Node
{
    /**
     * Constructor.
     *
     * @param \Twig_Node_Expression $block
     * @param string $context
     * @param int $line
     * @param string $tag
     */
    public function __construct(Twig_Node_Expression $block, $context, $line, $tag = null)
    {
        parent::__construct(
            array('block' => $block),
            array('context' => $context),
            $line,
            $tag
        );
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('$ngbmBlock = ')
            ->subcompile($this->getNode('block'))
            ->write(';' . PHP_EOL)
            ->write('if ($ngbmBlock instanceof ' . Block::class . ') {' . PHP_EOL)
            ->indent()
                ->write('$this->env->getExtension("' . RenderingExtension::class . '")->displayBlock($ngbmBlock, ')
                ->repr($this->getAttribute('context'))
                ->raw(', $this, $context, $blocks);' . PHP_EOL)
            ->outdent()
            ->write('}' . PHP_EOL);
    }
}
