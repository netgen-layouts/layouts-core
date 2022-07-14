<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Node;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\View\Twig\ContextualizedTwigTemplate;
use Netgen\Layouts\View\ViewInterface;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

use const PHP_EOL;

final class RenderZone extends Node
{
    public function __construct(AbstractExpression $zone, ?AbstractExpression $context = null, int $line = 0, ?string $tag = null)
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
            ->write('$nglZone = ')
                ->subcompile($this->getNode('zone'))
            ->write(';' . PHP_EOL)
            ->write('$nglZoneIdentifier = $nglZone instanceof ' . Zone::class . ' ? $nglZone->getIdentifier() : $nglZone;' . PHP_EOL);

        $this->compileContextNode($compiler);

        $compiler->write('$nglTemplate = new ' . ContextualizedTwigTemplate::class . '($this, $context, $blocks);' . PHP_EOL);
        $compiler->write('$this->env->getRuntime("' . RenderingRuntime::class . '")->displayZone($context["nglayouts"]->getLayout(), $nglZoneIdentifier, $nglContext, $nglTemplate);' . PHP_EOL);
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
                ->write('$nglContext = ')
                    ->subcompile($this->getNode('context'))
                ->write(';' . PHP_EOL);

            return;
        }

        $compiler->write('$nglContext = ' . ViewInterface::class . '::CONTEXT_DEFAULT;' . PHP_EOL);
    }
}
