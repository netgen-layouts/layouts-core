<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\NodeVisitor;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\DefaultContext as DefaultContextNode;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\RenderZone as RenderZoneNode;
use Symfony\Bridge\Twig\NodeVisitor\Scope;
use Twig\Environment;
use Twig\Node\BlockNode;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Expression\Variable\AssignContextVariable;
use Twig\Node\Expression\Variable\ContextVariable;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Node\SetNode;
use Twig\NodeVisitor\NodeVisitorInterface;

use function class_exists;
use function hash;
use function mt_rand;
use function sprintf;
use function uniqid;

final class DefaultContext implements NodeVisitorInterface
{
    private ?Scope $scope;

    public function __construct()
    {
        $this->scope = new Scope();
    }

    public function getPriority(): int
    {
        return -10;
    }

    public function enterNode(Node $node, Environment $env): Node
    {
        if (!$this->scope instanceof Scope) {
            return $node;
        }

        if ($node instanceof BlockNode || $node instanceof ModuleNode) {
            $this->scope = $this->scope->enter();
        }

        if ($node instanceof DefaultContextNode) {
            if ($node->getNode('expr') instanceof ConstantExpression) {
                $this->scope->set('context', $node->getNode('expr'));

                return $node;
            }

            $var = $this->getVarName();

            $assignContextVariable = class_exists(AssignContextVariable::class) ?
                new AssignContextVariable($var, $node->getTemplateLine()) :
                new AssignNameExpression($var, $node->getTemplateLine());

            $contextVariable = class_exists(ContextVariable::class) ?
                new ContextVariable($var, $node->getTemplateLine()) :
                new NameExpression($var, $node->getTemplateLine());

            $this->scope->set('context', $contextVariable);

            return new SetNode(
                false,
                new Node([$assignContextVariable]),
                new Node([$node->getNode('expr')]),
                $node->getTemplateLine(),
            );
        }

        if (!$this->scope->has('context')) {
            return $node;
        }

        if ($node instanceof RenderZoneNode && !$node->hasNode('context')) {
            $node->setNode('context', $this->scope->get('context'));
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env): ?Node
    {
        if ($node instanceof DefaultContextNode) {
            return null;
        }

        if ($this->scope instanceof Scope && ($node instanceof BlockNode || $node instanceof ModuleNode)) {
            $this->scope = $this->scope->leave();
        }

        return $node;
    }

    private function getVarName(): string
    {
        return sprintf('__internal_%s', hash('sha256', uniqid((string) mt_rand(), true), false));
    }
}
