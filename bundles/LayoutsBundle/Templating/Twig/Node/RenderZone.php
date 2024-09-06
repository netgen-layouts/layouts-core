<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Node;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\View\Twig\ContextualizedTwigTemplate;
use Netgen\Layouts\View\ViewInterface;
use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Environment;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

use const PHP_EOL;

#[YieldReady]
final class RenderZone extends Node
{
    public function __construct(AbstractExpression $zone, ?AbstractExpression $context = null, int $line = 0)
    {
        $nodes = ['zone' => $zone];
        if ($context instanceof AbstractExpression) {
            $nodes['context'] = $context;
        }

        parent::__construct($nodes, [], $line);
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
        $this->compileRenderNode($compiler);
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

    private function compileRenderNode(Compiler $compiler): void
    {
        if (Environment::VERSION_ID >= 30900) {
            $compiler->write('yield $this->env->getRuntime("' . RenderingRuntime::class . '")->renderZone($context["nglayouts"]->getLayout(), $nglZoneIdentifier, $nglContext, $nglTemplate);' . PHP_EOL);

            return;
        }

        $compiler->write('echo $this->env->getRuntime("' . RenderingRuntime::class . '")->renderZone($context["nglayouts"]->getLayout(), $nglZoneIdentifier, $nglContext, $nglTemplate);' . PHP_EOL);
    }
}
