<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node;

use Netgen\BlockManager\View\View\BlockView\ContextualizedTwigTemplate;
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
        $nodes = array('block' => $block);
        if ($context instanceof Twig_Node_Expression) {
            $nodes['context'] = $context;
        }

        parent::__construct($nodes, array(), $line, $tag);
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

        $this->compileContextNode($compiler);

        $compiler->write('$ngbmTemplate = new ' . ContextualizedTwigTemplate::class . '($this, $context, $blocks);' . PHP_EOL);

        $compiler
            ->write('if ($ngbmBlock instanceof ' . Block::class . ') {' . PHP_EOL)
            ->indent()
                ->write('$this->env->getExtension("' . RenderingExtension::class . '")->displayBlock($ngbmBlock, ')
                ->raw('array("twigTemplate" => $ngbmTemplate), $ngbmContext);' . PHP_EOL)
            ->outdent()
            ->write('}' . PHP_EOL);
    }
}
