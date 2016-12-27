<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node;

use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\BlockManager\Block\BlockDefinition\Twig\ContextualizedTwigTemplate;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Twig_Compiler;
use Twig_Node;
use Twig_Node_Expression;

class RenderZone extends Twig_Node
{
    use ContextTrait;

    /**
     * Constructor.
     *
     * @param \Twig_Node_Expression $zone
     * @param Twig_Node_Expression $context
     * @param int $line
     * @param string $tag
     */
    public function __construct(Twig_Node_Expression $zone, Twig_Node_Expression $context = null, $line = 0, $tag = null)
    {
        $nodes = array('zone' => $zone);
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
            ->write('$ngbmZone = ')
                ->subcompile($this->getNode('zone'))
            ->write(';' . PHP_EOL);

        $this->compileContextNode($compiler);

        $compiler->write('$ngbmTemplate = new ' . ContextualizedTwigTemplate::class . '($this, $context, $blocks);' . PHP_EOL);

        $compiler
            ->write('if ($ngbmZone instanceof ' . Zone::class . ') {' . PHP_EOL)
            ->indent()
                ->write('$this->env->getExtension("' . RenderingExtension::class . '")->displayZone($ngbmZone, ')
                ->raw('$ngbmContext, $ngbmTemplate);' . PHP_EOL)
            ->outdent()
            ->write('}' . PHP_EOL);
    }
}
