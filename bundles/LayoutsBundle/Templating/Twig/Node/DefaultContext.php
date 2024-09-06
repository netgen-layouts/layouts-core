<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Node;

use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

#[YieldReady]
final class DefaultContext extends Node
{
    public function __construct(AbstractExpression $expr, int $line = 0)
    {
        parent::__construct(['expr' => $expr], [], $line);
    }

    public function compile(Compiler $compiler): void
    {
        // noop as this node is just a marker for DefaultContext node visitor.
    }
}
