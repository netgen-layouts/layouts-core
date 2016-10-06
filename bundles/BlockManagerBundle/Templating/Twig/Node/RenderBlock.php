<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\BlockManager\API\Values\Page\Block;
use Twig_Node_Expression;
use Twig_Compiler;
use Twig_Node;

class RenderBlock extends Twig_Node
{
    use ContextTrait;

    /**
     * Constructor.
     *
     * @param \Twig_Node_Expression $block
     * @param Twig_Node_Expression $context
     * @param int $line
     * @param string $tag
     */
    public function __construct(Twig_Node_Expression $block, Twig_Node_Expression $context = null, $line = 0, $tag = null)
    {
        parent::__construct(
            array(
                'block' => $block,
                'context' => $context,
            ),
            array(),
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
            ->write(';' . PHP_EOL);

        $this->compileContextNode($compiler, $this->getNode('context'));

        $compiler
            ->write('if ($ngbmBlock instanceof ' . Block::class . ') {' . PHP_EOL)
            ->indent()
                ->write('$this->env->getExtension("' . RenderingExtension::class . '")->displayBlock($ngbmBlock, ')
                ->raw('$ngbmContext, $this, $context, $blocks);' . PHP_EOL)
            ->outdent()
            ->write('}' . PHP_EOL);
    }
}
