<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node;

use Netgen\BlockManager\View\ViewInterface;
use Twig_Compiler;
use Twig_Node;

trait ContextTrait
{
    /**
     * Compiles the context node.
     *
     * @param \Twig_Compiler $compiler
     */
    protected function compileContextNode(Twig_Compiler $compiler)
    {
        $contextNode = null;
        if ($this->hasNode('context')) {
            $contextNode = $this->getNode('context');
        }

        if ($contextNode instanceof Twig_Node) {
            $compiler
                ->write('$ngbmContext = ')
                    ->subcompile($this->getNode('context'))
                ->write(';' . PHP_EOL);

            return;
        }

        $compiler->write('$ngbmContext = ' . ViewInterface::class . '::CONTEXT_DEFAULT;' . PHP_EOL);
    }
}
