<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node;

use Twig_Node_Expression;
use Twig_Compiler;
use Twig_Node;

class RenderZone extends Twig_Node
{
    /**
     * Constructor.
     *
     * @param \Twig_Node_Expression $zone
     * @param string $context
     * @param int $line
     * @param string $tag
     */
    public function __construct(Twig_Node_Expression $zone, $context, $line, $tag = null)
    {
        parent::__construct(
            array('zone' => $zone),
            array('context' => $context),
            $line,
            $tag
        );
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('$ngbmZone = ')
            ->subcompile($this->getNode('zone'))
            ->write(';' . PHP_EOL)
            ->write('if ($ngbmZone instanceof \Netgen\BlockManager\API\Values\Page\Zone) {'. PHP_EOL)
            ->indent()
                ->write('$this->env->getExtension("netgen_block_manager")->displayZone($ngbmZone, ')
                ->repr($this->getAttribute('context'))
                ->raw(', $this, $context, $blocks);' . PHP_EOL)
            ->outdent()
            ->write('}' . PHP_EOL);
    }
}
