<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Exception\NotFoundException;
use Twig_SimpleFunction;
use Twig_Extension;

class PageExtension extends Twig_Extension
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ngbm_page';
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
                'ngbm_layout_name',
                array($this, 'getLayoutName')
            ),
        );
    }

    /**
     * Returns the layout name.
     *
     * @param int|string $layoutId
     *
     * @return string
     */
    public function getLayoutName($layoutId)
    {
        try {
            $layout = $this->layoutService->loadLayout($layoutId);

            return $layout->getName();
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
