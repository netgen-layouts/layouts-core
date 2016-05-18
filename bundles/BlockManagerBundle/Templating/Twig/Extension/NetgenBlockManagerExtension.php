<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\BlockManager\Block\BlockDefinition\TwigBlock;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone;
use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Netgen\Bundle\BlockManagerBundle\Renderer\BlockRendererInterface;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\View\ViewInterface;
use Twig_Extension_GlobalsInterface;
use Twig_SimpleFunction;
use Twig_Extension;
use Twig_Template;

class NetgenBlockManagerExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper
     */
    protected $globalHelper;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Renderer\BlockRendererInterface
     */
    protected $blockRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper $globalHelper
     * @param \Netgen\Bundle\BlockManagerBundle\Renderer\BlockRendererInterface $blockRenderer
     */
    public function __construct(
        GlobalHelper $globalHelper,
        BlockRendererInterface $blockRenderer
    ) {
        $this->globalHelper = $globalHelper;
        $this->blockRenderer = $blockRenderer;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'netgen_block_manager';
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'ngbm_render_zone',
                array($this, 'renderZone'),
                array(
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_block',
                array($this, 'renderBlock'),
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return \Twig_TokenParserInterface[]
     */
    public function getTokenParsers()
    {
        return array(
            new RenderZone(),
        );
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'ngbm' => $this->globalHelper,
        );
    }

    /**
     * Renders the provided zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param string $context
     *
     * @return string
     */
    public function renderZone(Zone $zone, $context = ViewInterface::CONTEXT_VIEW)
    {
        $html = '';

        foreach ($zone->getBlocks() as $block) {
            if ($block->getDefinitionIdentifier() !== TwigBlock::DEFINITION_IDENTIFIER) {
                $html .= $this->blockRenderer->renderBlockFragment($block, $context, array());
            }
        }

        return $html;
    }

    /**
     * Renders the provided block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     * @param string $context
     *
     * @return string
     */
    public function renderBlock(Block $block, array $parameters = array(), $context = ViewInterface::CONTEXT_VIEW)
    {
        return $this->blockRenderer->renderBlockFragment($block, $context, $parameters);
    }

    /**
     * Displays the provided zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param string $context
     * @param \Twig_Template $twigTemplate
     * @param array $twigContext
     * @param array $twigBocks
     */
    public function displayZone(
        Zone $zone,
        $context,
        Twig_Template $twigTemplate,
        $twigContext,
        array $twigBocks = array()
    ) {
        foreach ($zone->getBlocks() as $block) {
            if ($block->getDefinitionIdentifier() === TwigBlock::DEFINITION_IDENTIFIER) {
                $twigTemplate->displayBlock(
                    $block->getParameter('block_name'),
                    $twigContext,
                    $twigBocks
                );
            } else {
                $this->displayBlock($block, array(), $context);
            }
        }
    }

    /**
     * Displays the provided block.
     *
     * Used by "ngbm_render_zone" Twig tag, hence usage of "echo".
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     * @param string $context
     *
     * @return string
     */
    public function displayBlock(Block $block, array $parameters = array(), $context = ViewInterface::CONTEXT_VIEW)
    {
        echo $this->blockRenderer->renderBlockFragment($block, $context, $parameters);
    }
}
