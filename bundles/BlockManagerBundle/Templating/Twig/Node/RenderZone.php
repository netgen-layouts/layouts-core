<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node;

use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

class RenderZone extends Node
{
    /**
     * Constructor.
     *
     * @param \Twig\Node\Expression\AbstractExpression $zone
     * @param \Twig\Node\Expression\AbstractExpression $context
     * @param int $line
     * @param string $tag
     */
    public function __construct(AbstractExpression $zone, AbstractExpression $context = null, $line = 0, $tag = null)
    {
        $nodes = array('zone' => $zone);
        if ($context instanceof AbstractExpression) {
            $nodes['context'] = $context;
        }

        parent::__construct($nodes, array(), $line, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('$ngbmZone = ')
                ->subcompile($this->getNode('zone'))
            ->write(';' . PHP_EOL);

        $this->compileContextNode($compiler);

        $compiler->write('$ngbmTemplate = new ' . ContextualizedTwigTemplate::class . '($this, $context, $blocks);' . PHP_EOL);

        $compiler
            ->write('if ($ngbmZone instanceof ' . Zone::class . ') {' . PHP_EOL)
            ->indent()
                ->write('$this->env->getRuntime("' . RenderingRuntime::class . '")->displayZone($ngbmZone, ')
                ->raw('$ngbmContext, $ngbmTemplate);' . PHP_EOL)
            ->outdent()
            ->write('}' . PHP_EOL);
    }

    /**
     * Compiles the context node.
     *
     * @param \Twig\Compiler $compiler
     */
    private function compileContextNode(Compiler $compiler)
    {
        $contextNode = null;
        if ($this->hasNode('context')) {
            $contextNode = $this->getNode('context');
        }

        if ($contextNode instanceof Node) {
            $compiler
                ->write('$ngbmContext = ')
                    ->subcompile($this->getNode('context'))
                ->write(';' . PHP_EOL);

            return;
        }

        $compiler->write('$ngbmContext = ' . ViewInterface::class . '::CONTEXT_DEFAULT;' . PHP_EOL);
    }
}
