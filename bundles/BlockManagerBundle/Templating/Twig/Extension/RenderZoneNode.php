<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Twig_Node_Expression;
use Twig_Compiler;
use Twig_Node;

class RenderZoneNode extends Twig_Node
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
            ->raw(";\n")
            ->write('if (!$ngbmZone instanceof \Netgen\BlockManager\API\Values\Page\Zone) {' . "\n")
                ->indent()
                ->write('throw new \RuntimeException("Only zone can be rendered with ngbm_render_zone tag.");' . "\n")
            ->outdent()
            ->write("}\n\n")
            ->write('foreach ($ngbmZone->getBlocks() as $ngbmBlock) {' . "\n")
                ->indent()
                ->write('if ($ngbmBlock->getDefinitionIdentifier() === "content") {' . "\n")
                    ->indent()
                    ->write('$this->displayBlock($ngbmBlock->getParameters()["block_name"], $context, $blocks);' . "\n")
                ->outdent()
                ->write('} else {' . "\n")
                    ->indent()
                    ->write('$this->env->getExtension("netgen_block_manager")->displayBlock($ngbmBlock, array(), ')
                        ->repr($this->getAttribute('context'))
                        ->raw(");\n")
                ->outdent()
                ->write("}\n")
            ->outdent()
            ->write("}\n");
    }
}
