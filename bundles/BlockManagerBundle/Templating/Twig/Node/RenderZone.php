<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node;

use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

final class RenderZone extends Node
{
    public function __construct(AbstractExpression $zone, AbstractExpression $context = null, int $line = 0, string $tag = null)
    {
        $nodes = ['zone' => $zone];
        if ($context instanceof AbstractExpression) {
            $nodes['context'] = $context;
        }

        parent::__construct($nodes, [], $line, $tag);
    }

    public function compile(Compiler $compiler): void
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
     */
    private function compileContextNode(Compiler $compiler): void
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
